@extends('layouts.app')
@section('title', 'Data Deletion Policy - Doonspedo')
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
        <h1 class="header-title">Data Deletion Policy</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<h3 class='section-title'>1. Introduction</h3>
<p>At Doonspedo, we respect users’ privacy and provide mechanisms for users to request deletion of their personal information in accordance with applicable laws and platform requirements.</p>
<p>This Data Deletion Policy explains how users can request deletion of their account and personal data, how such requests are processed, and what information may be retained for legal, regulatory, and operational purposes.</p>
<h3 class='section-title'>2. Scope</h3>
<ul class='policy-list'>
<li>This Policy applies to all users of the Doonspedo platform, including:</li>
<li>Passengers</li>
<li>Drivers</li>
<li>Business Partners</li>
<li>Website Users</li>
<li>Mobile Application Users</li>
</ul>
<h3 class='section-title'>3. Information Eligible for Deletion</h3>
<p>Upon receiving a valid deletion request, Doonspedo may delete or anonymize personal information including:</p>
<ul class='policy-list'>
<li>User profile information</li>
<li>Name and contact details</li>
<li>Email address</li>
<li>Mobile number</li>
<li>Profile photographs</li>
<li>Saved preferences</li>
<li>App usage information</li>
<li>Device identifiers associated with the account</li>
<li>Other personal information not required for legal retention</li>
</ul>
<p>Deletion is subject to applicable legal, regulatory, and operational requirements.</p>
<h3 class='section-title'>4. How to Request Data Deletion</h3>
<p>Users may request account and data deletion through one of the following methods:</p>
<ul class='policy-list'>
<li>Option 1: In-App Deletion</li>
</ul>
<p>If available, users may submit a deletion request through the account settings section of the Doonspedo application.</p>
<ul class='policy-list'>
<li>Option 2: Email Request</li>
<li>Users may submit a deletion request by contacting:</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a></p>
<ul class='policy-list'>
<li>The request should include:</li>
<li>Full Name</li>
<li>Registered Mobile Number</li>
<li>Registered Email Address (if applicable)</li>
<li>Reason for deletion (optional)</li>
<li>Option 3: Customer Support</li>
</ul>
<p>Users may contact Doonspedo Customer Support and request account deletion assistance.</p>
<h3 class='section-title'>5. Identity Verification</h3>
<p>To protect user privacy and prevent unauthorized requests, Doonspedo may verify the identity of the requesting user before processing a deletion request.</p>
<ul class='policy-list'>
<li>Verification may include:</li>
<li>OTP verification</li>
<li>Registered mobile number verification</li>
<li>Email verification</li>
<li>Additional identity confirmation where necessary</li>
</ul>
<h3 class='section-title'>6. Processing Timeline</h3>
<ul class='policy-list'>
<li>Upon successful verification:</li>
</ul>
<p>Initial acknowledgment may be provided within a reasonable time.</p>
<p>Most deletion requests are processed within 30 days.</p>
<p>Complex requests may require additional processing time where permitted by law.</p>
<p>Users may be notified once deletion processing is completed.</p>
<h3 class='section-title'>7. Information We May Retain</h3>
<ul class='policy-list'>
<li>Certain information may be retained after account deletion where required for:</li>
<li>Legal Compliance</li>
<li>Government regulations</li>
<li>Court orders</li>
<li>Law enforcement requests</li>
<li>Tax and accounting obligations</li>
<li>Fraud Prevention</li>
<li>Fraud investigations</li>
<li>Security monitoring</li>
<li>Abuse prevention</li>
<li>Dispute Resolution</li>
<li>Ongoing claims</li>
<li>Chargebacks</li>
<li>Legal proceedings</li>
<li>Safety Requirements</li>
<li>Incident investigations</li>
<li>Safety-related reporting</li>
</ul>
<p>Retained information will be limited to what is necessary and permitted by applicable law.</p>
<h3 class='section-title'>8. Anonymized and Aggregated Data</h3>
<p>Doonspedo may retain anonymized, de-identified, or aggregated information that cannot reasonably identify an individual user.</p>
<ul class='policy-list'>
<li>Such information may be used for:</li>
<li>Analytics</li>
<li>Business reporting</li>
<li>Service improvement</li>
<li>Research and development</li>
</ul>
<p>This information is not considered personal data once properly anonymized.</p>
<h3 class='section-title'>9. Impact of Account Deletion</h3>
<ul class='policy-list'>
<li>After account deletion:</li>
<li>Users May Lose Access To:</li>
<li>Ride history</li>
<li>Earnings history</li>
<li>Saved payment methods</li>
<li>Rewards and loyalty points</li>
<li>Account preferences</li>
<li>Customer support history associated with the deleted account</li>
</ul>
<p>Deleted accounts generally cannot be restored once the deletion process has been completed.</p>
<p>Users wishing to use Doonspedo again may need to create a new account.</p>
<h3 class='section-title'>10. Driver Account Deletion</h3>
<ul class='policy-list'>
<li>Driver-partners requesting account deletion may be required to:</li>
</ul>
<p>Complete outstanding obligations.</p>
<p>Resolve pending disputes.</p>
<p>Clear unresolved payment matters.</p>
<p>Complete verification procedures.</p>
<p>Certain driver-related records may be retained as required by transportation, tax, financial, or regulatory obligations.</p>
<h3 class='section-title'>11. Security of Deleted Data</h3>
<p>Doonspedo follows reasonable security practices when handling deletion requests.</p>
<p>Deleted information is removed or anonymized using processes designed to protect user privacy and prevent unauthorized access.</p>
<h3 class='section-title'>12. Changes to This Policy</h3>
<p>Doonspedo may update this Data Deletion Policy from time to time.</p>
<p>Updated versions will be published through official channels, including our website and mobile applications.</p>
<p>Continued use of the Services following publication of updates constitutes acceptance of the revised Policy.</p>
<h3 class='section-title'>13. Contact Information</h3>
<ul class='policy-list'>
<li>For data deletion requests or privacy-related inquiries, please contact:</li>
<li>Doonspedo Privacy Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<h3 class='section-title'>14. Google Play Account Deletion Compliance</h3>
<p>In accordance with applicable Google Play requirements, users have the right to request:</p>
<p>Account deletion.</p>
<p>Deletion of personal information associated with their account.</p>
<p>Information regarding data retention practices.</p>
<p>Doonspedo provides accessible methods for users to submit deletion requests and manage their personal data.</p>
<p>By using Doonspedo Services, you acknowledge that you have read and understood this Data Deletion Policy.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
