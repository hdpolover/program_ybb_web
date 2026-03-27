<!-- Layout config Js -->
<script src="/assets/js/layout.js"></script>
<!-- Bootstrap Css -->
<link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />
<!-- Overlay fix: placed AFTER app.min.css so these rules win the cascade -->
<style>
/* Browser extension chat widget fix — its container covers the page but should not block clicks */
.chat-ai-widget-container {
    pointer-events: none !important;
}
.chat-ai-widget-container * {
    pointer-events: auto;
}
/* .bg-overlay is always decorative (backgrounds on auth/landing pages) — never intercept clicks */
.bg-overlay {
    pointer-events: none !important;
}

/* .vertical-overlay: non-interactive by default.
   Only restore pointer-events when it is intentionally shown (mobile sidebar/navbar open). */
.vertical-overlay {
    pointer-events: none !important;
}
body.vertical-sidebar-enable .vertical-overlay,
.navbar-show .vertical-overlay {
    pointer-events: auto !important;
}

/* On desktop: always hide the overlay entirely — it should never appear */
@media (min-width: 768px) {
    .vertical-overlay,
    body.vertical-sidebar-enable .vertical-overlay,
    .navbar-show .vertical-overlay {
        display: none !important;
        pointer-events: none !important;
    }
}
</style>
<!-- custom Css-->
<link href="/assets/css/custom.min.css" rel="stylesheet" type="text/css" />
<!-- DateTime Widget Css -->
<link href="/assets/css/datetime-widget.css" rel="stylesheet" type="text/css" />
<!-- Add to head -->
<link title="timeline-styles" rel="stylesheet" href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">
<script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>

<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://unpkg.com/bs-brain@2.0.4/tutorials/timelines/timeline-4/assets/css/timeline-4.css">