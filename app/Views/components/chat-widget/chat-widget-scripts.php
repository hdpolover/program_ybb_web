<!-- Chat Widget JavaScript -->
<script src="<?= base_url('assets/chat-widget/chat-api.js') ?>"></script>
<script src="<?= base_url('assets/chat-widget/chat-widget.js') ?>"></script>

<script>
    console.log('🎬 Chat widget script block loaded');
    
    // Initialize chat widget when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOM Content Loaded event fired');
        console.log('🔍 Checking if ChatWidget class is available...');
        console.log('ChatWidget type:', typeof ChatWidget);
        console.log('ChatAPI type:', typeof ChatAPI);
        
        if (typeof ChatWidget !== 'undefined') {
            console.log('✅ ChatWidget class found, initializing...');
            try {
                window.chatWidget = new ChatWidget();
                console.log('✅ Chat widget instance created successfully');
            } catch (error) {
                console.error('❌ Error creating ChatWidget instance:', error);
            }
        } else {
            console.error('❌ ChatWidget class not found!');
            console.log('🔍 Available global objects:', Object.keys(window).filter(key => key.toLowerCase().includes('chat')));
        }
    });
    
    // Also check if scripts have loaded
    console.log('🔍 Checking script loading status...');
    console.log('Document readyState:', document.readyState);
    
    // Fallback initialization if DOM is already loaded
    if (document.readyState === 'loading') {
        console.log('📄 Document still loading, waiting for DOMContentLoaded...');
    } else {
        console.log('📄 Document already loaded, checking ChatWidget availability...');
        setTimeout(() => {
            if (typeof ChatWidget !== 'undefined' && !window.chatWidget) {
                console.log('🔄 Attempting fallback initialization...');
                try {
                    window.chatWidget = new ChatWidget();
                    console.log('✅ Fallback initialization successful');
                } catch (error) {
                    console.error('❌ Fallback initialization failed:', error);
                }
            }
        }, 100);
    }
</script>