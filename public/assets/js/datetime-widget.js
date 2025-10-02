/**
 * DateTime Widget - Server-synced real-time clock and date display
 * Fetches server time from configured backend API and updates every second with timezone info
 */
class DateTimeWidget {
    constructor() {
        this.timeElement = document.getElementById('current-time');
        this.dateElement = document.getElementById('current-date');
        this.serverOffset = 0; // Difference between server and client time in milliseconds
        this.timezone = 'UTC';
        this.timezoneAbbr = 'UTC';
        this.timezoneOffset = '+00:00';
        this.lastSync = 0;
        this.syncInterval = 5 * 60 * 1000; // Sync with server every 5 minutes
        this.serverConnected = false; // Track server connection status
        this.serverFormattedTime = null;
        this.serverFormattedDate = null;
        
        this.init();
    }

    async init() {
        // Show loading state immediately
        this.updateDateTime();
        
        // Fetch server time initially - retry until successful
        let maxRetries = 3;
        let retryCount = 0;
        
        while (!this.serverConnected && retryCount < maxRetries) {
            await this.syncWithServer();
            
            if (!this.serverConnected) {
                retryCount++;
                console.log(`DateTime Widget: Retry ${retryCount}/${maxRetries} for server connection...`);
                
                if (retryCount < maxRetries) {
                    // Wait 2 seconds before retry
                    await new Promise(resolve => setTimeout(resolve, 2000));
                }
            }
        }
        
        // Update immediately after sync
        this.updateDateTime();
        
        // Update every second
        setInterval(() => {
            this.updateDateTime();
        }, 1000);
        
        // Sync with server periodically
        setInterval(() => {
            this.syncWithServer();
        }, this.syncInterval);
        
        // If still not connected after all retries, log critical error
        if (!this.serverConnected) {
            console.error('DateTime Widget: CRITICAL - Could not establish server connection after all retries');
            this.showError('Cannot connect to server');
        }
        
        // Setup click handler
        this.setupClickHandler();
    }
    
    async syncWithServer() {
        try {
            console.log('DateTime Widget: Syncing with server time endpoint...');
            
            const response = await fetch('/server-time', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                console.log('DateTime Widget: Server time response:', data);
                
                if (data.success && data.server_time) {
                    const serverTime = data.server_time;
                    const serverTimestamp = serverTime.timestamp * 1000; // Convert to milliseconds
                    const clientTimestamp = Date.now();
                    
                    // Calculate offset between server and client time
                    this.serverOffset = serverTimestamp - clientTimestamp;
                    
                    // Store server's formatted time directly
                    this.serverFormattedTime = serverTime.time_12;
                    this.serverFormattedDate = serverTime.date_formatted;
                    
                    // Use timezone info from server response
                    this.timezone = serverTime.timezone_name || serverTime.timezone?.name || 'UTC';
                    this.timezoneAbbr = serverTime.timezone?.abbreviation || 'UTC';
                    this.timezoneOffset = serverTime.timezone?.offset || '+00:00';
                    this.lastSync = clientTimestamp;
                    this.serverConnected = true;
                    
                    // Debug log the timezone data to verify structure
                    console.log('DateTime Widget: Timezone data received:', {
                        timezone_name: serverTime.timezone_name,
                        timezone_object: serverTime.timezone,
                        parsed_timezone: this.timezone,
                        parsed_abbr: this.timezoneAbbr,
                        parsed_offset: this.timezoneOffset
                    });
                    
                    // Clear any error states
                    this.clearError();
                    
                    console.log(`DateTime Widget: Successfully synced with server.`);
                    console.log(`- Server time: ${serverTime.time_12} ${serverTime.date_formatted}`);
                    console.log(`- Timezone: ${this.timezone} (${this.timezoneAbbr}) ${this.timezoneOffset}`);
                    console.log(`- Offset from client: ${this.serverOffset}ms`);
                } else {
                    console.error('DateTime Widget: Invalid response format from server:', data);
                    this.showError('Server time unavailable');
                    this.serverConnected = false;
                }
            } else {
                console.error(`DateTime Widget: Server returned ${response.status}: ${response.statusText}`);
                this.showError('Server connection failed');
                this.serverConnected = false;
            }
        } catch (error) {
            console.error('DateTime Widget: Failed to sync with server:', error);
            this.showError('Server unreachable');
            this.serverConnected = false;
        }
    }

    updateDateTime() {
        // Only display time if we have a server connection
        if (!this.serverConnected) {
            // Show waiting message until server connection is established
            if (this.timeElement) {
                this.timeElement.textContent = 'Loading...';
            }
            if (this.dateElement) {
                this.dateElement.textContent = 'Official Server Time';
            }
            return;
        }
        
        if (this.timeElement) {
            // Calculate the current seconds since last sync to update the time
            const secondsSinceSync = Math.floor((Date.now() - this.lastSync) / 1000);
            
            if (this.serverFormattedTime) {
                // Parse the server time and add elapsed seconds
                const serverTime = this.parseServerTime(this.serverFormattedTime);
                if (serverTime) {
                    serverTime.setSeconds(serverTime.getSeconds() + secondsSinceSync);
                    
                    // Format using the same 12-hour format as backend
                    const timeOptions = {
                        hour: 'numeric',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: true
                    };
                    this.timeElement.textContent = serverTime.toLocaleTimeString('en-US', timeOptions);
                } else {
                    // If parsing fails, show error instead of fallback
                    this.timeElement.textContent = 'Time Parse Error';
                }
            } else {
                this.timeElement.textContent = 'Server Time Missing';
            }
        }
        
        if (this.dateElement) {
            // Only use server's formatted date - no client fallback
            if (this.serverFormattedDate && this.timezone && this.timezoneOffset) {
                this.dateElement.textContent = `${this.serverFormattedDate} (${this.timezone} ${this.timezoneOffset})`;
            } else {
                this.dateElement.textContent = 'Official Server Time';
            }
        }
    }
    
    // Helper method to parse server time format (e.g., "2:35:23 PM")
    parseServerTime(timeString) {
        try {
            // Create a date object for today and set the time
            const today = new Date();
            const [time, ampm] = timeString.split(' ');
            const [hours, minutes, seconds] = time.split(':').map(Number);
            
            let hour24 = hours;
            if (ampm === 'PM' && hours !== 12) {
                hour24 += 12;
            } else if (ampm === 'AM' && hours === 12) {
                hour24 = 0;
            }
            
            today.setHours(hour24, minutes, seconds || 0, 0);
            return today;
        } catch (error) {
            console.warn('DateTime Widget: Failed to parse server time:', timeString, error);
            return null;
        }
    }
    
    // Show error state in the widget
    showError(message) {
        console.error('DateTime Widget Error:', message);
        if (this.timeElement) {
            this.timeElement.textContent = 'Server Error';
            this.timeElement.classList.add('text-danger');
        }
        if (this.dateElement) {
            this.dateElement.textContent = message;
            this.dateElement.classList.add('text-muted');
        }
    }
    
    // Clear error state
    clearError() {
        if (this.timeElement) {
            this.timeElement.classList.remove('text-danger');
        }
        if (this.dateElement) {
            this.dateElement.classList.remove('text-muted');
        }
    }
    
    // Add click handler for the widget
    setupClickHandler() {
        const widget = document.querySelector('.datetime-widget');
        if (widget) {
            widget.addEventListener('click', () => {
                this.handleClick();
            });
        }
    }
    
    // Handle widget click - show detailed time info
    handleClick() {
        if (!this.serverConnected) {
            alert('Server time is not available. Please check your connection.');
            return;
        }
        
        const lastSyncTime = this.lastSync ? new Date(this.lastSync).toLocaleString() : 'Never';
        const offsetHours = Math.round(this.serverOffset / 1000 / 60 / 60 * 100) / 100;
        
        const info = `Official Server Time Details:
        
🕒 Current Time: ${this.serverFormattedTime || 'N/A'}
📅 Current Date: ${this.serverFormattedDate || 'N/A'}
🌍 Timezone: ${this.timezone} (${this.timezoneAbbr}) ${this.timezoneOffset}
⏱️ Last Sync: ${lastSyncTime}
🔄 Time Offset: ${offsetHours}h from your device
        
This is the official reference time for all deadlines and submissions.`;
        
        alert(info);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create widget instance and expose it globally for testing
    window.dateTimeWidget = new DateTimeWidget();
});