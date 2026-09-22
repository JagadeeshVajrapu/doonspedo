@extends('layouts.app')
@section('title', 'Privacy Policy - Doonspedo')
@section('styles')
<style>
    .header-section { background: linear-gradient(135deg, var(--primary-color, #cddc29) 0%, #a4b31a 100%); padding: 60px 20px; text-align: center; border-bottom-left-radius: 40px; border-bottom-right-radius: 40px; box-shadow: 0 10px 30px rgba(205, 220, 41, 0.3); margin-bottom: 40px; }
    .header-title { font-weight: 800; font-size: 2.5rem; color: #1a1a1a; margin-bottom: 10px; }
    .last-updated { font-weight: 500; color: #4a4a4a; background: rgba(255, 255, 255, 0.5); padding: 5px 15px; border-radius: 20px; display: inline-block; }
    .content-card { background: var(--bg-card, #ffffff); border-radius: 20px; padding: 40px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05); margin-bottom: 40px; transition: transform 0.3s ease; }
    .content-card:hover { transform: translateY(-5px); }
    .section-title { font-weight: 700; margin-top: 40px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--border-color, #f0f0f0); position: relative; }
    .section-title::after { content: ''; position: absolute; left: 0; bottom: -2px; height: 3px; width: 60px; background: var(--primary-color, #cddc29); border-radius: 3px; }
    .sub-title { font-weight: 600; margin-top: 25px; margin-bottom: 15px; }
    ul.policy-list { list-style-type: none; padding-left: 0; }
    ul.policy-list li { position: relative; padding-left: 30px; margin-bottom: 10px; }
    ul.policy-list li::before { content: '\F26A'; font-family: 'bootstrap-icons'; position: absolute; left: 0; color: var(--primary-color, #cddc29); font-size: 1.1rem; }
    p { margin-bottom: 15px; }
</style>
@endsection
@section('content')
    @include('layouts.header')
    <div class="header-section">
        <h1 class="header-title">Privacy Policy</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<h3 class='section-title'>1. Introduction</h3>
<p>Welcome to Doonspedo (“Company”, “we”, “our”, or “us”). We are committed to protecting your privacy and ensuring that your personal information is handled securely and responsibly.</p>
<p>This Privacy Policy explains how Doonspedo collects, uses, stores, shares, and protects information when you use our mobile applications, websites, and related services (collectively, the “Services”).</p>
<p>By accessing or using our Services, you agree to the collection and use of information in accordance with this Privacy Policy.</p>
<h3 class='section-title'>2. Information We Collect</h3>
<h5 class='sub-title'>A. Information You Provide</h5>
<ul class='policy-list'>
<li>When you create an account or use our Services, we may collect:</li>
<li>Full name</li>
<li>Mobile phone number</li>
<li>Email address</li>
<li>Profile photograph (if provided)</li>
<li>Government-issued identification documents (for driver verification)</li>
<li>Vehicle details and registration information (for drivers)</li>
<li>Customer support communications</li>
</ul>
<h5 class='sub-title'>B. Location Information</h5>
<ul class='policy-list'>
<li>To provide ride-booking and navigation services, we may collect:</li>
<li>Precise location data</li>
<li>Background location data (when permitted by the user)</li>
<li>Pickup and drop-off locations</li>
<li>Route and trip information</li>
</ul>
<p>Location access may be required even when the application is not actively in use if necessary for ride tracking, driver availability, safety features, or trip management.</p>
<h5 class='sub-title'>C. Payment Information</h5>
<ul class='policy-list'>
<li>We may collect:</li>
<li>Payment transaction details</li>
<li>Payment method information</li>
<li>Wallet transaction records</li>
<li>Billing information</li>
</ul>
<p>Payment card information is generally processed by secure third-party payment providers and is not stored directly by us unless required by law.</p>
<h5 class='sub-title'>D. Device and Technical Information</h5>
<ul class='policy-list'>
<li>We may automatically collect:</li>
<li>Device model and manufacturer</li>
<li>Operating system version</li>
<li>Device identifiers</li>
<li>IP address</li>
<li>Mobile network information</li>
<li>Application version</li>
<li>Crash logs and diagnostic information</li>
</ul>
<h5 class='sub-title'>E. Usage Information</h5>
<ul class='policy-list'>
<li>We may collect:</li>
<li>Ride history</li>
<li>Search history within the app</li>
<li>App interactions</li>
<li>Features used</li>
<li>Service preferences</li>
</ul>
<h3 class='section-title'>3. How We Use Your Information</h3>
<ul class='policy-list'>
<li>We use information to:</li>
</ul>
<p>Create and manage user accounts.</p>
<p>Provide ride-booking services.</p>
<p>Connect riders with drivers.</p>
<p>Process payments.</p>
<p>Verify identity and eligibility.</p>
<p>Improve platform performance and functionality.</p>
<p>Provide customer support.</p>
<p>Detect fraud and unauthorized activity.</p>
<p>Comply with legal obligations.</p>
<p>Ensure user and platform safety.</p>
<p>Send service-related notifications and updates.</p>
<h3 class='section-title'>4. Legal Basis for Processing</h3>
<ul class='policy-list'>
<li>Where applicable, we process personal information based on:</li>
</ul>
<p>User consent.</p>
<p>Performance of a contract.</p>
<p>Compliance with legal obligations.</p>
<p>Legitimate business interests.</p>
<p>Protection of user safety and security.</p>
<h3 class='section-title'>5. Sharing of Information</h3>
<ul class='policy-list'>
<li>We may share information with:</li>
<li>Service Providers</li>
<li>Third-party vendors who assist with:</li>
<li>Payment processing</li>
<li>Cloud hosting</li>
<li>Analytics</li>
<li>Customer support</li>
<li>Communication services</li>
<li>Drivers and Riders</li>
</ul>
<p>Certain information may be shared between drivers and riders to facilitate transportation services.</p>
<ul class='policy-list'>
<li>Government and Legal Authorities</li>
<li>We may disclose information when:</li>
</ul>
<p>Required by law.</p>
<p>Requested by authorized government agencies.</p>
<p>Necessary to protect rights, safety, or property.</p>
<ul class='policy-list'>
<li>Business Transfers</li>
</ul>
<p>Information may be transferred as part of a merger, acquisition, restructuring, or asset sale.</p>
<p>We do not sell users’ personal information to third parties.</p>
<h3 class='section-title'>6. Data Retention</h3>
<ul class='policy-list'>
<li>We retain information for as long as necessary to:</li>
</ul>
<p>Provide Services.</p>
<p>Maintain business records.</p>
<p>Resolve disputes.</p>
<p>Prevent fraud.</p>
<p>Comply with legal obligations.</p>
<p>Retention periods may vary depending on the type of information and applicable laws.</p>
<h3 class='section-title'>7. Data Security</h3>
<p>We implement reasonable administrative, technical, and organizational safeguards designed to protect personal information from:</p>
<ul class='policy-list'>
<li>Unauthorized access</li>
<li>Loss</li>
<li>Misuse</li>
<li>Disclosure</li>
<li>Alteration</li>
<li>Destruction</li>
</ul>
<p>However, no electronic storage or internet transmission method can be guaranteed to be completely secure.</p>
<h3 class='section-title'>8. Children’s Privacy</h3>
<p>Our Services are not directed to individuals under the age permitted by applicable law.</p>
<p>We do not knowingly collect personal information from children. If we become aware that information has been collected from a child without proper authorization, we will take appropriate steps to delete such information.</p>
<h3 class='section-title'>9. User Rights</h3>
<ul class='policy-list'>
<li>Subject to applicable law, users may have the right to:</li>
</ul>
<p>Access personal information.</p>
<p>Correct inaccurate information.</p>
<p>Request deletion of personal information.</p>
<p>Withdraw consent where applicable.</p>
<p>Object to certain processing activities.</p>
<p>Request information regarding data handling practices.</p>
<p>Requests may be submitted through our support channels.</p>
<h3 class='section-title'>10. Account Deletion</h3>
<p>Users may request deletion of their account and associated personal information by:</p>
<p>Using available account deletion features within the application (if provided), or</p>
<p>Contacting customer support.</p>
<p>Certain information may be retained where required by law, fraud prevention requirements, dispute resolution needs, taxation obligations, or regulatory compliance.</p>
<h3 class='section-title'>11. Third-Party Services</h3>
<p>Our Services may contain links to third-party websites, applications, or services.</p>
<p>We are not responsible for the privacy practices of third-party platforms and encourage users to review their privacy policies separately.</p>
<h3 class='section-title'>12. Cookies and Similar Technologies</h3>
<ul class='policy-list'>
<li>Our website and digital services may use:</li>
<li>Cookies</li>
<li>Analytics technologies</li>
<li>Device identifiers</li>
<li>Similar tracking technologies</li>
</ul>
<p>These technologies help improve user experience, security, and service functionality.</p>
<h3 class='section-title'>13. International Data Transfers</h3>
<p>Where permitted by law, information may be processed or stored in locations outside the user’s jurisdiction. Appropriate safeguards will be implemented when required.</p>
<h3 class='section-title'>14. Changes to This Privacy Policy</h3>
<p>We may update this Privacy Policy periodically.</p>
<p>Updated versions will be posted through our website, applications, or other official communication channels. Continued use of the Services after changes become effective constitutes acceptance of the revised Privacy Policy.</p>
<h3 class='section-title'>15. Contact Us</h3>
<p>For questions, concerns, privacy requests, or account deletion requests, please contact:</p>
<ul class='policy-list'>
<li>Doonspedo Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<p>By using Doonspedo Services, you acknowledge that you have read, understood, and agreed to this Privacy Policy.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
