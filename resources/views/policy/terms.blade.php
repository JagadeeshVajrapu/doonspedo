@extends('layouts.app')
@section('title', 'Terms and Conditions - Doonspedo')
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
        <h1 class="header-title">Terms and Conditions</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<h3 class='section-title'>1. Acceptance of Terms</h3>
<p>Welcome to Doonspedo (“Doonspedo”, “Company”, “we”, “our”, or “us”).</p>
<p>These Terms and Conditions (“Terms”) govern your access to and use of the Doonspedo mobile applications, websites, software, and related services (collectively, the “Services”).</p>
<p>By creating an account, accessing, or using the Services, you agree to be bound by these Terms. If you do not agree with these Terms, you must not use the Services.</p>
<h3 class='section-title'>2. About Doonspedo</h3>
<p>Doonspedo is a technology platform that connects passengers with independent drivers for transportation services.</p>
<p>Doonspedo facilitates ride bookings through its platform but does not provide transportation services directly unless otherwise stated. Transportation services are provided by independent driver-partners.</p>
<h3 class='section-title'>3. Eligibility</h3>
<ul class='policy-list'>
<li>To use the Services, you must:</li>
</ul>
<p>Be at least 18 years old or the age required under applicable law.</p>
<p>Have the legal capacity to enter into binding agreements.</p>
<p>Provide accurate, complete, and current registration information.</p>
<p>Maintain the security of your account credentials.</p>
<p>The Company reserves the right to suspend or terminate accounts that provide false or misleading information.</p>
<h3 class='section-title'>4. User Accounts</h3>
<ul class='policy-list'>
<li>Users are responsible for:</li>
</ul>
<p>Maintaining the confidentiality of login credentials.</p>
<p>All activities conducted through their account.</p>
<p>Providing accurate and updated information.</p>
<p>You must immediately notify us of any unauthorized use of your account.</p>
<h3 class='section-title'>5. Ride Booking Services</h3>
<ul class='policy-list'>
<li>For Passengers</li>
</ul>
<p>Passengers may request transportation services through the platform.</p>
<ul class='policy-list'>
<li>The availability of rides depends on:</li>
<li>Driver availability</li>
<li>Geographic location</li>
<li>Traffic conditions</li>
<li>Technical availability of the platform</li>
</ul>
<p>Doonspedo does not guarantee ride availability at all times.</p>
<ul class='policy-list'>
<li>For Drivers</li>
<li>Drivers must:</li>
</ul>
<p>Hold valid driving licenses.</p>
<p>Maintain required permits and registrations.</p>
<p>Maintain valid insurance coverage.</p>
<p>Comply with all applicable transportation laws and regulations.</p>
<h3 class='section-title'>6. Fare Policy</h3>
<ul class='policy-list'>
<li>Ride fares are calculated using various factors including:</li>
<li>Distance travelled</li>
<li>Estimated travel time</li>
<li>Traffic conditions</li>
<li>Demand and supply conditions</li>
<li>Applicable taxes and government charges</li>
<li>Fare estimates displayed before booking may change due to:</li>
<li>Route changes</li>
<li>Additional stops</li>
<li>Waiting time</li>
<li>Toll charges</li>
<li>Traffic conditions</li>
</ul>
<h3 class='section-title'>7. Surge Pricing</h3>
<p>During periods of increased demand, including peak hours, holidays, festivals, adverse weather conditions, or limited driver availability, dynamic pricing may apply.</p>
<p>Users acknowledge and agree that fares may increase during such periods in accordance with applicable regulations.</p>
<h3 class='section-title'>8. Payment Terms</h3>
<ul class='policy-list'>
<li>Accepted payment methods may include:</li>
<li>Cash</li>
<li>UPI</li>
<li>Credit Cards</li>
<li>Debit Cards</li>
<li>Wallet Payments</li>
<li>Other approved payment methods</li>
</ul>
<p>Users authorize Doonspedo and its payment partners to process payments for services rendered.</p>
<p>Failure to complete payment may result in account restrictions or suspension.</p>
<h3 class='section-title'>9. Driver Subscription Program</h3>
<p>Doonspedo may offer subscription-based access to drivers.</p>
<ul class='policy-list'>
<li>Available plans may include:</li>
<li>Daily Plans</li>
<li>Weekly Plans</li>
<li>Monthly Plans</li>
<li>Annual Plans</li>
</ul>
<p>Subscription fees are displayed transparently within the platform.</p>
<p>Drivers are responsible for renewing subscriptions before expiration to maintain access to ride requests.</p>
<h3 class='section-title'>10. Cancellation Policy</h3>
<ul class='policy-list'>
<li>Passenger Cancellation</li>
<li>Cancellation fees may apply if:</li>
</ul>
<p>A ride is cancelled after driver assignment.</p>
<p>The driver has already travelled toward the pickup location.</p>
<p>Cancellation occurs beyond the permitted free cancellation period.</p>
<ul class='policy-list'>
<li>Driver Cancellation</li>
</ul>
<p>Drivers may be subject to performance reviews, temporary restrictions, or account actions for excessive cancellations without valid reasons.</p>
<h3 class='section-title'>11. Refund Policy</h3>
<p>Subscription fees are generally non-refundable once activated.</p>
<ul class='policy-list'>
<li>Refunds may be considered only in exceptional circumstances including:</li>
<li>Duplicate payments</li>
<li>Technical errors</li>
<li>Incorrect charges verified by the Company</li>
</ul>
<p>Approved refunds will be processed according to applicable payment provider timelines.</p>
<h3 class='section-title'>12. User Conduct</h3>
<ul class='policy-list'>
<li>Users agree not to:</li>
</ul>
<p>Violate any law or regulation.</p>
<p>Harass, threaten, or abuse others.</p>
<p>Discriminate against any person.</p>
<p>Use fraudulent payment methods.</p>
<p>Create fake accounts.</p>
<p>Damage vehicles or property.</p>
<p>Interfere with platform operations.</p>
<p>Upload malicious software or harmful content.</p>
<p>Violation of these Terms may result in suspension or permanent account termination.</p>
<h3 class='section-title'>13. Safety Requirements</h3>
<ul class='policy-list'>
<li>For safety purposes, users may be required to:</li>
</ul>
<p>Verify identity.</p>
<p>Provide accurate information.</p>
<p>Follow safety instructions.</p>
<p>Cooperate with investigations relating to incidents or complaints.</p>
<p>Emergency and safety features may be provided within the application but do not guarantee prevention of all incidents.</p>
<h3 class='section-title'>14. Ratings and Reviews</h3>
<p>Passengers and drivers may provide ratings and reviews after completed trips.</p>
<ul class='policy-list'>
<li>The Company reserves the right to remove content that:</li>
</ul>
<p>Violates applicable laws.</p>
<p>Contains offensive material.</p>
<p>Includes false, misleading, or abusive content.</p>
<h3 class='section-title'>15. Intellectual Property</h3>
<ul class='policy-list'>
<li>All rights, title, and interest in the Doonspedo platform, including:</li>
<li>Logos</li>
<li>Trademarks</li>
<li>Software</li>
<li>Designs</li>
<li>Content</li>
<li>Technology</li>
</ul>
<p>remain the exclusive property of Doonspedo or its licensors.</p>
<p>Users may not copy, modify, distribute, reverse engineer, or exploit platform content without prior written permission.</p>
<h3 class='section-title'>16. Privacy</h3>
<p>The collection, use, and disclosure of personal information are governed by the Doonspedo Privacy Policy.</p>
<p>By using the Services, users consent to the practices described in the Privacy Policy.</p>
<h3 class='section-title'>17. Suspension and Termination</h3>
<p>Doonspedo reserves the right to suspend, restrict, or terminate accounts at its sole discretion where:</p>
<p>These Terms are violated.</p>
<p>Fraudulent activity is suspected.</p>
<p>Required documentation becomes invalid.</p>
<p>User behavior threatens safety or platform integrity.</p>
<p>Termination may occur without prior notice where permitted by law.</p>
<h3 class='section-title'>18. Limitation of Liability</h3>
<ul class='policy-list'>
<li>To the maximum extent permitted by applicable law:</li>
</ul>
<p>Doonspedo provides the platform on an “as available” basis.</p>
<p>The Company does not guarantee uninterrupted or error-free service.</p>
<p>The Company shall not be liable for indirect, incidental, consequential, special, or punitive damages arising from the use of the Services.</p>
<p>Nothing in these Terms excludes liability that cannot be excluded under applicable law.</p>
<h3 class='section-title'>19. Indemnification</h3>
<p>Users agree to indemnify and hold harmless Doonspedo, its affiliates, directors, employees, and partners from claims, losses, liabilities, damages, and expenses arising from:</p>
<p>Violation of these Terms.</p>
<p>Misuse of the Services.</p>
<p>Violation of applicable laws.</p>
<p>Infringement of third-party rights.</p>
<h3 class='section-title'>20. Changes to Terms</h3>
<p>Doonspedo may modify these Terms at any time.</p>
<p>Updated Terms will be published through the application, website, or other official communication channels.</p>
<p>Continued use of the Services following publication of updated Terms constitutes acceptance of the revised Terms.</p>
<h3 class='section-title'>21. Governing Law and Jurisdiction</h3>
<p>These Terms shall be governed by and interpreted in accordance with the laws of India.</p>
<p>Any dispute arising out of or relating to these Terms shall be subject to the exclusive jurisdiction of the competent courts located in the jurisdiction where the Company is registered.</p>
<h3 class='section-title'>22. Contact Information</h3>
<ul class='policy-list'>
<li>For questions regarding these Terms and Conditions, please contact:</li>
<li>Doonspedo Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<p>By accessing or using Doonspedo Services, you acknowledge that you have read, understood, and agreed to be bound by these Terms and Conditions.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
