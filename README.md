# Custom Elementor Contact Form v1.3.3

This version adds diagnostics for WordPress mail and Brevo troubleshooting.

## Diagnostic page

After activation, go to:

**WordPress → Tools → Custom Contact Form**

It shows:

- Whether `wp_mail()` is available
- Whether a Brevo WordPress plugin appears to be active
- The WordPress default recipient
- A direct **Send Test Email** button
- A **Mail Debug Log**

The direct test bypasses Elementor and calls `wp_mail()` directly. If Brevo SMTP is active, the Brevo WordPress plugin should route the message through Brevo. Brevo documents that its WordPress plugin can route WordPress transactional emails through Brevo SMTP. 

## How to diagnose the contact form

1. Open **Tools → Custom Contact Form**.
2. Enter your own email address.
3. Click **Send Test Email**.
4. Check **Brevo → Transactional → Logs**.
5. If the test appears in Brevo, WordPress → Brevo is working.
6. Submit the Elementor form.
7. Return to **Tools → Custom Contact Form** and inspect **Mail Debug Log**.
8. Submit the form again and check Brevo Transactional → Logs.

If the direct test reaches Brevo but the form does not, the problem is in the form submission path rather than the Brevo SMTP connection.

## Email configuration in Elementor

Open the widget:

**Content → Email Settings**

Configure:

- Send Submissions To
- From Name
- From Email
- Email Subject

The visitor's address is used as Reply-To.

## SMTP

The plugin does not store SMTP credentials. It uses WordPress `wp_mail()`. An existing SMTP/API mail plugin such as Brevo can therefore handle the actual delivery.

Do not enter your Brevo SMTP key into this plugin.


## v1.3.2 email improvements

- Diagnostic test subject is now `Vaultcore Contact Form — Mail Delivery Test`.
- Form subject uses the Elementor **Email Subject** setting.
- The notification email is formatted as a readable HTML message containing name, email, message, submission time, and site information.
- The visitor's email is set as `Reply-To`, so replying to the notification replies directly to the visitor.
- When the Brevo WordPress plugin is active, the form does not force its own `From:` header; Brevo can use the verified sender selected in Brevo.
- When Brevo is not active, the Elementor **From Name** and **From Email** settings are used.


## v1.3.3

- Adds a responsive **Field Left Padding** control under **Style → Layout** for the name, email, and message fields.
- Updates the plugin author to **JM Custom Web Dev & IT**.
- Adds a **Configure/test emails** link to the plugin description, opening **Tools → Custom Contact Form**.
