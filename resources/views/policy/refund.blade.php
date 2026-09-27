@extends('layouts.app')
@section('title', 'Refund & Cancellation Policy - Doonspedo')
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
        <h1 class="header-title">Refund & Cancellation Policy</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<ul class='policy-list'>
<li>REFUND &amp; CANCELLATION POLICY</li>
</ul>
<h3 class='section-title'>1. Introduction</h3>
<p>This Refund &amp; Cancellation Policy governs ride cancellations, subscription cancellations, refunds, and related transactions on the Doonspedo platform.</p>
<p>By using Doonspedo services, you agree to the terms outlined in this policy.</p>
<h3 class='section-title'>2. Ride Cancellation Policy</h3>
<h3 class='section-title'>2.1 Passenger Cancellation</h3>
<p>Passengers may cancel a ride at any time through the Doonspedo application.</p>
<ul class='policy-list'>
<li>However, a cancellation fee may be charged if:</li>
</ul>
<p>The ride is cancelled after a driver has accepted the booking.</p>
<p>The driver has already traveled toward the pickup location.</p>
<p>The cancellation occurs after the free cancellation period specified in the application.</p>
<p>The applicable cancellation fee, if any, will be displayed within the application.</p>
<h3 class='section-title'>2.2 Driver Cancellation</h3>
<p>Drivers may cancel a ride only for legitimate reasons, including but not limited to:</p>
<p>Passenger unavailability.</p>
<p>Incorrect pickup location.</p>
<p>Safety concerns.</p>
<p>Vehicle breakdown or emergency situations.</p>
<ul class='policy-list'>
<li>Repeated cancellations without valid reasons may result in:</li>
</ul>
<p>Reduced ride allocation.</p>
<p>Temporary account restrictions.</p>
<p>Suspension or termination of driver access.</p>
<h3 class='section-title'>3. Subscription Cancellation Policy</h3>
<p>Doonspedo operates a subscription-based model for driver-partners.</p>
<ul class='policy-list'>
<li>Available subscription plans may include:</li>
<li>Daily Plans</li>
<li>Weekly Plans</li>
<li>Monthly Plans</li>
<li>Annual Plans</li>
</ul>
<p>Drivers may choose not to renew their subscriptions at any time.</p>
<p>Cancellation of future renewals does not affect the validity of the current active subscription period.</p>
<h3 class='section-title'>4. Subscription Refund Policy</h3>
<h3 class='section-title'>4.1 Non-Refundable Subscription Fees</h3>
<p>Unless otherwise required by applicable law, subscription fees are generally non-refundable once:</p>
<p>The subscription has been activated.</p>
<p>Platform access has been granted.</p>
<p>Ride request access has become available.</p>
<p>Unused subscription periods are generally not eligible for partial refunds.</p>
<h3 class='section-title'>4.2 Exceptional Refund Cases</h3>
<ul class='policy-list'>
<li>Refund requests may be reviewed in circumstances such as:</li>
</ul>
<p>Duplicate payments.</p>
<p>Technical system errors.</p>
<p>Incorrect billing caused by platform malfunction.</p>
<p>Unauthorized transactions verified by investigation.</p>
<p>Approval of refunds remains at the sole discretion of Doonspedo, subject to applicable laws.</p>
<h3 class='section-title'>5. Ride Fare Refunds</h3>
<ul class='policy-list'>
<li>Refunds related to ride fares may be considered in situations including:</li>
</ul>
<p>Incorrect fare calculations.</p>
<p>Duplicate fare charges.</p>
<p>Payment processing errors.</p>
<p>Service-related disputes verified through investigation.</p>
<p>Refund eligibility will be determined after review of trip records, payment records, and supporting information.</p>
<h3 class='section-title'>6. Failed Transactions</h3>
<ul class='policy-list'>
<li>If a payment fails but funds are deducted from a user’s account:</li>
</ul>
<p>The transaction may be automatically reversed by the payment provider.</p>
<p>Processing times depend on the respective bank, UPI provider, wallet provider, or payment gateway.</p>
<p>Users may contact support if a reversal is not received within the applicable processing period.</p>
<h3 class='section-title'>7. Refund Processing Timeline</h3>
<ul class='policy-list'>
<li>Approved refunds are generally processed within:</li>
</ul>
<p>7 to 15 business days for card payments.</p>
<p>3 to 10 business days for UPI transactions.</p>
<p>Timelines may vary depending on banks, payment gateways, and financial institutions.</p>
<p>Doonspedo is not responsible for delays caused by third-party financial service providers.</p>
<h3 class='section-title'>8. Non-Refundable Charges</h3>
<ul class='policy-list'>
<li>The following are generally non-refundable:</li>
</ul>
<p>Activated subscription fees.</p>
<p>Cancellation charges applied according to platform policy.</p>
<p>Promotional purchases or special offers.</p>
<p>Charges resulting from policy violations.</p>
<p>Completed ride services.</p>
<h3 class='section-title'>9. Fraud Prevention</h3>
<ul class='policy-list'>
<li>Doonspedo reserves the right to:</li>
</ul>
<p>Investigate refund requests.</p>
<p>Request supporting documentation.</p>
<p>Reject fraudulent or abusive refund claims.</p>
<p>Suspend accounts involved in suspected payment fraud.</p>
<p>Any misuse of refund processes may result in permanent account suspension.</p>
<h3 class='section-title'>10. Changes to This Policy</h3>
<p>Doonspedo reserves the right to modify this Refund &amp; Cancellation Policy at any time.</p>
<p>Updated versions will be published through the application, website, or official communication channels.</p>
<p>Continued use of the platform after updates constitutes acceptance of the revised policy.</p>
<h3 class='section-title'>11. Contact Us</h3>
<p>For refund requests, billing concerns, payment disputes, or cancellation-related assistance, please contact:</p>
<ul class='policy-list'>
<li>Doonspedo Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<p>By using Doonspedo services, you acknowledge that you have read, understood, and agreed to this Refund &amp; Cancellation Policy.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
