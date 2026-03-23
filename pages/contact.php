<?php
/**
 * Doji Funding — Contact Page
 */
?>

<section class="section" style="padding-top:48px">
<div class="section-inner" style="max-width:900px;margin:0 auto">

    <h1 class="page-title">Contact <span class="green">Us</span></h1>
    <p class="page-subtitle">We're here to help. Reach out through any of the channels below.</p>

    <div class="section-divider"></div>

    <div class="contact-grid">

        <!-- Contact channels -->
        <div>
            <div class="contact-card">
                <div class="contact-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 7L2 7"/></svg></div>
                <h3>General Support</h3>
                <p>Questions about your account, challenges, or trading rules.</p>
                <a href="mailto:support@dojifunding.com" class="contact-link">support@dojifunding.com</a>
                <span class="contact-meta">Response time: within 24 hours</span>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon"><?= icon('coins') ?></div>
                <h3>Billing & Payouts</h3>
                <p>Payment issues, refund requests, or payout inquiries.</p>
                <a href="mailto:billing@dojifunding.com" class="contact-link">billing@dojifunding.com</a>
                <span class="contact-meta">Response time: within 24 hours</span>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
                <h3>Affiliate Program</h3>
                <p>Partnership inquiries, affiliate support, or custom deals.</p>
                <a href="mailto:affiliates@dojifunding.com" class="contact-link">affiliates@dojifunding.com</a>
                <span class="contact-meta">Response time: within 48 hours</span>
            </div>

            <div class="contact-card">
                <div class="contact-card-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round"><path d="M12 3v18"/><path d="M2 12h4l4-9 4 9h4"/><circle cx="6" cy="16" r="2"/><circle cx="18" cy="16" r="2"/></svg></div>
                <h3>Legal & Compliance</h3>
                <p>Terms of service, privacy, data requests, or legal matters.</p>
                <a href="mailto:legal@dojifunding.com" class="contact-link">legal@dojifunding.com</a>
                <span class="contact-meta">Response time: within 48 hours</span>
            </div>
        </div>

        <!-- Contact form -->
        <div>
            <div class="contact-form-wrap">
                <h3 style="margin-bottom:20px">Send Us a Message</h3>
                <form id="contactForm" onsubmit="submitContactForm(event)">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" name="name" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" name="email" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Subject</label>
                        <select class="form-input" name="subject" required>
                            <option value="">Select a topic...</option>
                            <option value="account">Account & Dashboard</option>
                            <option value="challenge">Challenge & Trading Rules</option>
                            <option value="payout">Payouts & Billing</option>
                            <option value="technical">Technical Issue</option>
                            <option value="affiliate">Affiliate Program</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Message</label>
                        <textarea class="form-input" name="message" rows="5" required placeholder="Describe your question or issue..."></textarea>
                    </div>
                    <div id="contactError" style="display:none;color:#e74c3c;font-size:13px;margin-bottom:12px"></div>
                    <button type="submit" class="btn-primary-lg" id="contactBtn" style="width:100%">
                        <span class="contact-btn-text">Send Message</span>
                        <span class="contact-btn-loader" style="display:none">Sending&hellip;</span>
                    </button>
                    <div id="contactSuccess" class="contact-success" style="display:none">
                        <?= icon('check-circle', 14) ?> Message sent successfully! We'll get back to you within 24 hours.
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="section-divider"></div>

    <!-- Community -->
    <div class="contact-community">
        <h2 style="text-align:center;margin-bottom:8px">Join Our <span class="green">Community</span></h2>
        <p style="text-align:center;color:var(--text3);font-size:14px;margin-bottom:24px">Get instant help, share strategies, and connect with fellow traders.</p>

        <div class="contact-social-grid">
            <a href="https://discord.gg/kNUqAqCppU" target="_blank" class="contact-social-card discord">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286z"/></svg>
                <span>Discord Community</span>
                <span class="contact-social-sub">Real-time support & trading chat</span>
            </a>
            <a href="https://x.com/DojiFunding" target="_blank" class="contact-social-card x">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                <span>X (Twitter)</span>
                <span class="contact-social-sub">Updates & announcements</span>
            </a>
            <a href="https://www.instagram.com/dojifunding/" target="_blank" class="contact-social-card instagram">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                <span>Instagram</span>
                <span class="contact-social-sub">Daily trading content</span>
            </a>
            <a href="https://www.youtube.com/@DojiFunding" target="_blank" class="contact-social-card youtube">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/><path d="M9.545 15.568V8.432L15.818 12l-6.273 3.568z" fill="var(--bg)"/></svg>
                <span>YouTube</span>
                <span class="contact-social-sub">Tutorials & market analysis</span>
            </a>
        </div>
    </div>

    <div style="height:32px"></div>

    <!-- Company info -->
    <div style="text-align:center;color:var(--text3);font-size:12px;line-height:1.8">
        <strong>Volatys Dynamics LTD</strong> — Operating as Doji Funding®<br>
        For urgent matters, please use email. We aim to respond to all inquiries within 24 hours during business days.
    </div>

</div>
</section>

<script>
function submitContactForm(e) {
    e.preventDefault();

    var form = document.getElementById('contactForm');
    var btn = document.getElementById('contactBtn');
    var errEl = document.getElementById('contactError');
    var successEl = document.getElementById('contactSuccess');

    // Clear previous messages
    errEl.style.display = 'none';
    errEl.textContent = '';
    successEl.style.display = 'none';

    // Client-side validation
    var name = (form.name.value || '').trim();
    var email = (form.email.value || '').trim();
    var subject = form.subject.value;
    var message = (form.message.value || '').trim();
    var errors = [];

    if (!name) errors.push('Full Name is required.');
    if (!email) {
        errors.push('Email Address is required.');
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.push('Please enter a valid email address.');
    }
    if (!subject) errors.push('Please select a subject.');
    if (!message) {
        errors.push('Message is required.');
    } else if (message.length < 10) {
        errors.push('Message must be at least 10 characters.');
    }

    if (errors.length > 0) {
        errEl.textContent = errors[0];
        errEl.style.display = 'block';
        return;
    }

    // Disable button and show loading state
    btn.disabled = true;
    btn.querySelector('.contact-btn-text').style.display = 'none';
    btn.querySelector('.contact-btn-loader').style.display = 'inline';
    form.style.opacity = '0.6';

    // Simulate form submission (no backend endpoint yet)
    setTimeout(function() {
        form.style.opacity = '1';
        btn.disabled = false;
        btn.querySelector('.contact-btn-text').style.display = 'inline';
        btn.querySelector('.contact-btn-loader').style.display = 'none';
        successEl.style.display = 'block';
        form.reset();
        setTimeout(function() {
            successEl.style.display = 'none';
        }, 5000);
    }, 1500);
}
</script>

<?php include 'includes/community.php'; ?>
