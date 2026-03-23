# Dubai Tourist Leads Landing Page

A PHP landing page inspired by the provided design, with a styled application form and direct SMTP email delivery.

## Files

- `index.php` — landing page markup and form processing.
- `styles.css` — all page styling and responsive layout.
- `smtp_mailer.php` — lightweight SMTP client implemented with PHP sockets.
- `config.php` — SMTP credentials and notification settings.

## Setup

1. Update `config.php` with your SMTP host, port, username, password, sender email, and destination email.
2. Serve the project with PHP, for example:

   ```bash
   php -S 127.0.0.1:8000
   ```

3. Open `http://127.0.0.1:8000` in your browser.

## Notes

- The design uses a remote skyline background image for the hero and CTA sections.
- Form submissions are sent through SMTP instead of PHP `mail()`.
