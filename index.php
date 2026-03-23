<?php
require_once __DIR__ . '/smtp_mailer.php';

$config = require __DIR__ . '/config.php';
$success = '';
$error = '';
$formData = [
    'full_name' => '',
    'business_name' => '',
    'website' => '',
    'business_type' => '',
    'monthly_budget' => '',
    'best_time' => '',
    'lead_goal' => '',
    'whatsapp' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formData as $key => $value) {
        $formData[$key] = trim($_POST[$key] ?? '');
    }

    $requiredFields = [
        'full_name' => 'Full name',
        'business_name' => 'Business name',
        'website' => 'Website / Instagram',
        'business_type' => 'Type of business',
        'monthly_budget' => 'Monthly ad budget',
        'best_time' => 'Best time to call',
        'lead_goal' => 'Lead target',
        'whatsapp' => 'WhatsApp number',
    ];

    foreach ($requiredFields as $field => $label) {
        if ($formData[$field] === '') {
            $error = "$label is required.";
            break;
        }
    }

    if ($error === '' && !filter_var($config['notification_email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Notification email is not configured correctly.';
    }

    if ($error === '') {
        $subject = 'New Dubai Tourism Leads Application';
        $htmlMessage = '<h2>New Free Trial Application</h2>'
            . '<table cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;font-family:Arial,sans-serif;">'
            . '<tr><th align="left">Full name</th><td>' . htmlspecialchars($formData['full_name']) . '</td></tr>'
            . '<tr><th align="left">Business name</th><td>' . htmlspecialchars($formData['business_name']) . '</td></tr>'
            . '<tr><th align="left">Website / Instagram</th><td>' . htmlspecialchars($formData['website']) . '</td></tr>'
            . '<tr><th align="left">Type of business</th><td>' . htmlspecialchars($formData['business_type']) . '</td></tr>'
            . '<tr><th align="left">Monthly ad budget</th><td>' . htmlspecialchars($formData['monthly_budget']) . '</td></tr>'
            . '<tr><th align="left">Best time to call</th><td>' . htmlspecialchars($formData['best_time']) . '</td></tr>'
            . '<tr><th align="left">Lead target</th><td>' . htmlspecialchars($formData['lead_goal']) . '</td></tr>'
            . '<tr><th align="left">WhatsApp number</th><td>' . htmlspecialchars($formData['whatsapp']) . '</td></tr>'
            . '</table>';
        $textMessage = "New Free Trial Application\n\n"
            . "Full name: {$formData['full_name']}\n"
            . "Business name: {$formData['business_name']}\n"
            . "Website / Instagram: {$formData['website']}\n"
            . "Type of business: {$formData['business_type']}\n"
            . "Monthly ad budget: {$formData['monthly_budget']}\n"
            . "Best time to call: {$formData['best_time']}\n"
            . "Lead target: {$formData['lead_goal']}\n"
            . "WhatsApp number: {$formData['whatsapp']}\n";

        try {
            send_smtp_mail($config, [
                'from_email' => $config['from_email'],
                'from_name' => $config['from_name'],
                'to_email' => $config['notification_email'],
                'to_name' => 'Sales Team',
                'reply_to' => $config['from_email'],
                'subject' => $subject,
                'html' => $htmlMessage,
                'text' => $textMessage,
            ]);
            $success = 'Application submitted successfully. We will contact you shortly.';
            foreach ($formData as $key => $value) {
                $formData[$key] = '';
            }
        } catch (Throwable $exception) {
            $error = 'Unable to send your application right now. Please review your SMTP settings in config.php.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dubai Tourist Leads — Free Trial</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="topbar">⚡ Only 3 trial slots available this month — <a href="#apply">Apply Now</a></div>

    <header class="hero section skyline-bg">
        <div class="overlay"></div>
        <div class="container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Free Trial — Dubai Tourism Lead Generation</p>
                <h1>Get <span>10–20 tourist enquiries</span> for your Dubai business</h1>
                <p class="lead">We help Dubai-based travel, tours, desert safari, yacht rental, and luxury experience brands generate qualified inbound leads.</p>
                <ul class="check-list">
                    <li>No upfront risk. No long-term contracts.</li>
                    <li>See real enquiries before you pay anything.</li>
                </ul>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="#apply">Check if you qualify — free trial</a>
                    <a class="btn btn-ghost" href="#apply">Chat on WhatsApp</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="section panel">
            <div class="container">
                <p class="section-tag">⚠ Qualification Required</p>
                <h2>This free trial is not for everyone</h2>
                <p class="section-subtitle">We only work with businesses that meet these criteria.</p>
                <div class="three-grid cards">
                    <article class="card"><span class="number">01</span><p>Already operating a travel or experience business in Dubai.</p></article>
                    <article class="card"><span class="number">02</span><p>Ready to handle incoming enquiries immediately via WhatsApp or calls.</p></article>
                    <article class="card"><span class="number">03</span><p>Can invest in ads after the trial if results are good.</p></article>
                </div>
                <p class="footnote">👉 If you're serious about scaling bookings, you'll qualify.</p>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>Who this is for</h2>
                <div class="two-grid cards split-cards">
                    <article class="card good">
                        <h3>This is for you</h3>
                        <ul>
                            <li>Travel agencies</li>
                            <li>Tour operators</li>
                            <li>Desert safari companies</li>
                            <li>Yacht rental businesses</li>
                            <li>Luxury experience providers</li>
                        </ul>
                    </article>
                    <article class="card bad">
                        <h3>This is not for you</h3>
                        <ul>
                            <li>Newly launched pages</li>
                            <li>Businesses with zero process for handling leads</li>
                            <li>Anyone expecting free leads forever</li>
                            <li>Agencies offering marketing as a reseller</li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        <section class="section panel">
            <div class="container">
                <h2>What you get in the <span>free trial</span></h2>
                <p class="section-subtitle">We don't just run ads — we set up a complete lead generation system.</p>
                <div class="three-grid cards icon-cards">
                    <article class="card"><h3>Google Ads campaign</h3><p>Target tourists actively searching for Dubai tours, desert safaris, and yacht rentals.</p></article>
                    <article class="card"><h3>Meta ads campaign</h3><p>Reach travelers planning Dubai trips across Instagram and Facebook.</p></article>
                    <article class="card"><h3>Conversion landing setup</h3><p>Mobile-first page built for high-intent traffic and direct WhatsApp enquiries.</p></article>
                    <article class="card"><h3>WhatsApp lead flow</h3><p>Instant enquiry capture so your sales team can reply in minutes.</p></article>
                    <article class="card"><h3>Performance tracking</h3><p>Track lead volume, campaign health, and qualified conversion trends.</p></article>
                    <article class="card"><h3>Expert strategy</h3><p>Offer, targeting, and ad message recommendations based on your niche.</p></article>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>How the <span>free trial</span> works</h2>
                <div class="steps">
                    <div class="step"><span>1</span><strong>Apply</strong><small>Fill out the form below.</small></div>
                    <div class="step"><span>2</span><strong>Qualification call</strong><small>We review your offer and niche.</small></div>
                    <div class="step"><span>3</span><strong>Campaign setup</strong><small>We launch a custom lead system.</small></div>
                    <div class="step"><span>4</span><strong>Receive enquiries</strong><small>You get real tourist leads.</small></div>
                    <div class="step"><span>5</span><strong>Continue if happy</strong><small>No pressure. Only scale if it works.</small></div>
                </div>
            </div>
        </section>

        <section class="section panel">
            <div class="container narrow">
                <h2>Why we offer a <span>free trial</span></h2>
                <div class="three-grid cards mini-cards">
                    <article class="card muted">Most agencies charge upfront.</article>
                    <article class="card muted">Results are not guaranteed.</article>
                    <article class="card muted">Local trust is often low.</article>
                </div>
                <article class="card contrast-card">
                    <h3>We do the opposite.</h3>
                    <p>We prove results first — then you decide.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="container narrow">
                <h2>Frequently asked <span>questions</span></h2>
                <div class="faq-list">
                    <details class="faq-item"><summary>How many leads will I get?</summary><p>Qualified businesses typically see 10–20 enquiries during the initial trial period, depending on niche, offer, and speed to lead.</p></details>
                    <details class="faq-item"><summary>Do I need to spend on ads?</summary><p>Yes. The trial covers our setup and strategy. If you qualify, you will need a real ad budget so leads can be generated.</p></details>
                    <details class="faq-item"><summary>How fast can I start?</summary><p>Most approved businesses can be reviewed within 24 hours and launched shortly after the qualification call.</p></details>
                    <details class="faq-item"><summary>What happens after the trial?</summary><p>If you like the lead quality, we continue on a paid monthly basis and scale what is already working.</p></details>
                </div>
            </div>
        </section>

        <section class="section apply-section" id="apply">
            <div class="container narrow">
                <h2>Apply for your <span>free trial</span></h2>
                <p class="section-subtitle">Limited slots available. If you're a fit, we'll reach out within 24 hours.</p>

                <?php if ($success !== ''): ?>
                    <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form class="application-form card" method="post" action="#apply">
                    <div class="form-grid">
                        <label>
                            <span>Full name *</span>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($formData['full_name']); ?>" placeholder="John Smith" required>
                        </label>
                        <label>
                            <span>Business name *</span>
                            <input type="text" name="business_name" value="<?php echo htmlspecialchars($formData['business_name']); ?>" placeholder="Desert Adventure LLC" required>
                        </label>
                        <label class="full-width">
                            <span>Website / Instagram *</span>
                            <input type="text" name="website" value="<?php echo htmlspecialchars($formData['website']); ?>" placeholder="www.yourbusiness.com or @yourbusiness" required>
                        </label>
                        <label>
                            <span>Type of business *</span>
                            <input type="text" name="business_type" value="<?php echo htmlspecialchars($formData['business_type']); ?>" placeholder="Tours / Yacht / Safari" required>
                        </label>
                        <label>
                            <span>Monthly ad budget *</span>
                            <input type="text" name="monthly_budget" value="<?php echo htmlspecialchars($formData['monthly_budget']); ?>" placeholder="AED 3,000 / month" required>
                        </label>
                        <label>
                            <span>Best time to call *</span>
                            <input type="text" name="best_time" value="<?php echo htmlspecialchars($formData['best_time']); ?>" placeholder="2 PM to 5 PM" required>
                        </label>
                        <label>
                            <span>Lead target *</span>
                            <input type="text" name="lead_goal" value="<?php echo htmlspecialchars($formData['lead_goal']); ?>" placeholder="10-20 leads / month" required>
                        </label>
                        <label class="full-width">
                            <span>WhatsApp number *</span>
                            <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($formData['whatsapp']); ?>" placeholder="+971 50 000 0000" required>
                        </label>
                    </div>
                    <button class="btn btn-primary submit-btn" type="submit">Submit application</button>
                    <p class="footnote">⚡ Only serious businesses ready to scale will be accepted.</p>
                </form>
            </div>
        </section>
    </main>

    <section class="section cta-banner skyline-bg">
        <div class="overlay"></div>
        <div class="container narrow cta-content">
            <h2>Stop depending on OTAs.</h2>
            <p>Start getting direct bookings.</p>
            <div class="hero-actions centered">
                <a class="btn btn-primary" href="#apply">Apply now — limited slots available</a>
                <a class="btn btn-ghost" href="#apply">Chat on WhatsApp for faster approval</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-row">
            <strong>DubaiTouristLeads</strong>
            <p>© <?php echo date('Y'); ?> DubaiTouristLeads — using email via SMTP.</p>
        </div>
    </footer>
</body>
</html>
