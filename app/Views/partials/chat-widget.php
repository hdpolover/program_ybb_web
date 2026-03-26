<!-- Chat AI Widget -->
<?php if(getenv('CHAT_WIDGET_ENABLED') !== 'false'): ?>
<script
  src="<?= getenv('CHAT_WIDGET_URL') ?: 'https://aksamu.com/chat-widget.js' ?>"
  data-bot-id="<?= getenv('CHAT_WIDGET_BOT_ID') ?: '4a9ea369-4638-413f-92d4-9c4600f7c6be' ?>"
  data-primary-color="<?= getenv('CHAT_WIDGET_PRIMARY_COLOR') ?: '#16a34a' ?>"
  data-button-shape="<?= getenv('CHAT_WIDGET_BUTTON_SHAPE') ?: 'circle' ?>"
  defer
></script>
<?php endif; ?>
