@extends('layouts.app')
@section('title', 'Account Deletion Request - Doonspedo')
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
        <h1 class="header-title">Account Deletion Request</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<ul class='policy-list'>
<li>DELETE MY ACCOUNT</li>
<li>Account &amp; Data Deletion Request</li>
</ul>
<p>At Doonspedo, we respect your privacy and provide users with the ability to request deletion of their account and personal information.</p>
<p>If you no longer wish to use Doonspedo, you may submit a request to permanently delete your account and associated personal data.</p>
<p>What Happens When You Delete Your Account?</p>
<ul class='policy-list'>
<li>When your account deletion request is approved:</li>
</ul>
<p>Your Doonspedo account will be permanently deactivated.</p>
<p>Access to the Doonspedo application will be removed.</p>
<p>Personal profile information associated with your account may be deleted or anonymized.</p>
<p>Saved preferences and account settings may be removed.</p>
<p>Loyalty points, rewards, promotional benefits, and account-related features may no longer be available.</p>
<p>Please note that account deletion is generally irreversible.</p>
<ul class='policy-list'>
<li>Information That May Be Retained</li>
</ul>
<p>Certain information may be retained for legal, regulatory, security, fraud prevention, taxation, accounting, dispute resolution, and compliance purposes, including:</p>
<ul class='policy-list'>
<li>Transaction records</li>
<li>Payment records</li>
<li>Ride history required by law</li>
<li>Fraud prevention records</li>
<li>Safety-related reports</li>
<li>Information required to comply with legal obligations</li>
</ul>
<p>Any retained information will be handled in accordance with applicable laws and our Privacy Policy.</p>
<ul class='policy-list'>
<li>How to Request Account Deletion</li>
<li>To request account deletion, please send an email to:</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a></p>
<ul class='policy-list'>
<li>Include the Following Information:</li>
<li>Full Name</li>
<li>Registered Mobile Number</li>
<li>Registered Email Address (if applicable)</li>
<li>User Type (Passenger or Driver)</li>
<li>Subject Line: “Account Deletion Request”</li>
<li>Sample Email Format</li>
<li>Subject: Account Deletion Request</li>
<li>Dear Doonspedo Support,</li>
</ul>
<p>I would like to request the permanent deletion of my Doonspedo account and associated personal data.</p>
<ul class='policy-list'>
<li>Name: __________</li>
<li>Registered Mobile Number: __________</li>
<li>Registered Email Address: __________</li>
<li>User Type: Passenger / Driver</li>
</ul>
<p>I confirm that I am the owner of this account and request its deletion in accordance with the Doonspedo Privacy Policy.</p>
<p>Thank you.</p>
<ul class='policy-list'>
<li>Identity Verification</li>
</ul>
<p>For security purposes, Doonspedo may verify your identity before processing the request.</p>
<ul class='policy-list'>
<li>Verification may include:</li>
<li>OTP verification</li>
<li>Mobile number verification</li>
<li>Email verification</li>
<li>Additional identity confirmation where necessary</li>
<li>Processing Time</li>
</ul>
<p>Most account deletion requests are processed within 30 days of successful identity verification.</p>
<p>In certain situations, additional time may be required where permitted by applicable law.</p>
<p>Need Help?</p>
<p>If you have questions regarding account deletion, data privacy, or your personal information, please contact:</p>
<ul class='policy-list'>
<li>Doonspedo Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<p>By submitting an account deletion request, you acknowledge that certain information may be retained where required by law and that deleted accounts generally cannot be restored once the deletion process has been completed.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
