/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Version: 4.3.0
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Common Plugins Js File
*/

//Common plugins
if(document.querySelectorAll("[toast-list]") || document.querySelectorAll('[data-choices]') || document.querySelectorAll("[data-provider]")){ 
  // Load scripts dynamically instead of using document.write
  const scripts = [
    'https://cdn.jsdelivr.net/npm/toastify-js',
    'assets/libs/choices.js/public/assets/scripts/choices.min.js',
    'assets/libs/flatpickr/flatpickr.min.js'
  ];
  
  scripts.forEach(src => {
    if (!document.querySelector(`script[src="${src}"]`)) {
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = src;
      document.head.appendChild(script);
    }
  });
}