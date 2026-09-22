@extends('layouts.app')
@section('title', 'About Us - Doonspedo')
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
        <h1 class="header-title">About Us</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<ul class='policy-list'>
<li>Welcome to Doonspedo</li>
</ul>
<p>Doonspedo is a modern mobility and transportation technology platform dedicated to making travel safer, smarter, and more accessible for everyone. We connect passengers with verified independent drivers through an easy-to-use digital platform that provides reliable ride-booking services across cities and communities.</p>
<p>Founded with a vision to create a fair and transparent transportation ecosystem, Doonspedo empowers drivers through a subscription-based model that allows them to retain 100% of their ride earnings without paying ride commissions. By eliminating commission-based deductions, we help drivers maximize their income while providing affordable transportation options for passengers.</p>
<ul class='policy-list'>
<li>Our Mission</li>
</ul>
<p>Our mission is to transform urban and local transportation by delivering safe, affordable, and technology-driven mobility solutions while creating sustainable earning opportunities for drivers.</p>
<ul class='policy-list'>
<li>We strive to:</li>
</ul>
<p>Provide convenient and dependable transportation services.</p>
<p>Support drivers with a transparent and commission-free earning model.</p>
<p>Enhance passenger safety through advanced technology and safety features.</p>
<p>Deliver exceptional customer experiences.</p>
<p>Promote trust, transparency, and fairness within the mobility industry.</p>
<ul class='policy-list'>
<li>Our Vision</li>
</ul>
<p>To become one of India’s most trusted, innovative, and driver-friendly mobility platforms, improving everyday transportation through technology, transparency, and customer-focused services.</p>
<ul class='policy-list'>
<li>What We Offer</li>
<li>For Passengers</li>
</ul>
<p>Quick and convenient ride booking.</p>
<p>Real-time driver tracking.</p>
<p>Transparent fare estimates.</p>
<p>Multiple payment options.</p>
<p>Safety and emergency support features.</p>
<p>Reliable transportation services.</p>
<ul class='policy-list'>
<li>For Drivers</li>
</ul>
<p>Commission-free earnings.</p>
<p>Flexible subscription plans.</p>
<p>Access to ride requests through the platform.</p>
<p>Transparent pricing structure.</p>
<p>Driver support and assistance.</p>
<p>Fair and sustainable earning opportunities.</p>
<ul class='policy-list'>
<li>Safety First</li>
</ul>
<p>At Doonspedo, safety remains a top priority. We continuously work to improve rider and driver safety through:</p>
<p>GPS-enabled trip tracking.</p>
<p>Driver verification processes.</p>
<p>Emergency assistance features.</p>
<p>Secure payment systems.</p>
<p>Customer support services.</p>
<p>While no system can completely eliminate risk, we are committed to implementing technologies and practices that help create a safer experience for all users.</p>
<ul class='policy-list'>
<li>Innovation and Technology</li>
</ul>
<p>Doonspedo leverages modern technology to simplify transportation and improve the overall user experience. Our platform is designed to provide efficient ride matching, seamless payments, reliable navigation, and responsive customer support.</p>
<p>We continuously invest in innovation to improve service quality, operational efficiency, and user satisfaction.</p>
<ul class='policy-list'>
<li>Customer Commitment</li>
<li>Our customers are at the center of everything we do. We are committed to:</li>
</ul>
<p>Transparency in pricing and policies.</p>
<p>Responsive customer support.</p>
<p>Continuous platform improvements.</p>
<p>Fair treatment of riders and drivers.</p>
<p>Reliable and accessible transportation services.</p>
<ul class='policy-list'>
<li>Join the Doonspedo Community</li>
</ul>
<p>Whether you are a passenger looking for a dependable ride or a driver seeking better earning opportunities, Doonspedo is committed to providing a transportation platform built on trust, technology, and transparency.</p>
<p>Together, we are driving the future of mobility.</p>
<p>DoonspedoMoving People. Empowering Drivers.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
