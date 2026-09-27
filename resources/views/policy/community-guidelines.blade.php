@extends('layouts.app')
@section('title', 'Community Guidelines - Doonspedo')
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
        <h1 class="header-title">Community Guidelines</h1>
        <span class="last-updated">Last Updated: June 2026</span>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card">
<ul class='policy-list'>
<li>DOONSPEDO COMMUNITY GUIDELINES</li>
</ul>
<h3 class='section-title'>1. Purpose</h3>
<p>At Doonspedo, we are committed to creating a safe, respectful, and trustworthy environment for passengers, drivers, partners, and all members of our community.</p>
<p>These Community Guidelines establish the standards of behavior expected from everyone using the Doonspedo platform. By accessing or using our services, you agree to follow these guidelines.</p>
<p>Failure to comply may result in warnings, temporary suspension, permanent account removal, or legal action where applicable.</p>
<h3 class='section-title'>2. Respect Everyone</h3>
<p>All users must treat others with dignity, courtesy, and respect.</p>
<ul class='policy-list'>
<li>Expected Behavior</li>
</ul>
<p>Be polite and professional.</p>
<p>Communicate respectfully.</p>
<p>Respect personal boundaries.</p>
<p>Cooperate during rides.</p>
<p>Follow lawful instructions when appropriate.</p>
<ul class='policy-list'>
<li>Unacceptable Behavior</li>
</ul>
<p>Verbal abuse.</p>
<p>Threatening language.</p>
<p>Intimidation.</p>
<p>Bullying.</p>
<p>Offensive gestures.</p>
<p>Hate speech.</p>
<p>Aggressive conduct.</p>
<p>Any behavior that creates fear, discomfort, or harm may result in immediate action.</p>
<h3 class='section-title'>3. Zero Tolerance for Harassment</h3>
<p>Doonspedo maintains a strict zero-tolerance policy regarding harassment.</p>
<ul class='policy-list'>
<li>Harassment includes but is not limited to:</li>
</ul>
<p>Unwanted physical contact.</p>
<p>Sexual advances.</p>
<p>Inappropriate comments.</p>
<p>Repeated unwanted communication.</p>
<p>Stalking.</p>
<p>Offensive remarks about appearance, gender, religion, nationality, disability, or other personal characteristics.</p>
<p>Users found engaging in harassment may be permanently removed from the platform.</p>
<h3 class='section-title'>4. Anti-Discrimination Policy</h3>
<p>Discrimination is strictly prohibited.</p>
<ul class='policy-list'>
<li>Users may not discriminate against anyone based on:</li>
<li>Race</li>
<li>Color</li>
<li>National origin</li>
<li>Religion</li>
<li>Language</li>
<li>Disability</li>
<li>Age</li>
<li>Gender</li>
<li>Marital status</li>
<li>Any other characteristic protected by law</li>
</ul>
<p>Every user deserves equal treatment and access to services.</p>
<h3 class='section-title'>5. Safety First</h3>
<p>Safety is a shared responsibility.</p>
<ul class='policy-list'>
<li>Riders Should:</li>
</ul>
<p>Verify vehicle and driver details before entering.</p>
<p>Wear seat belts whenever available.</p>
<p>Avoid distracting the driver.</p>
<p>Follow reasonable safety instructions.</p>
<ul class='policy-list'>
<li>Drivers Should:</li>
</ul>
<p>Obey all traffic laws.</p>
<p>Drive safely and responsibly.</p>
<p>Keep vehicles roadworthy and clean.</p>
<p>Verify passenger pickup details.</p>
<p>Avoid distracted driving.</p>
<p>Any activity that endangers safety may result in account suspension.</p>
<h3 class='section-title'>6. Illegal Activities Are Prohibited</h3>
<p>Users must not use Doonspedo for illegal purposes.</p>
<ul class='policy-list'>
<li>Prohibited activities include:</li>
</ul>
<p>Drug-related activities.</p>
<p>Human trafficking.</p>
<p>Transportation of illegal goods.</p>
<p>Fraudulent transactions.</p>
<p>Theft.</p>
<p>Criminal conduct.</p>
<p>Any violation of applicable law.</p>
<p>Doonspedo may cooperate with law enforcement authorities when legally required.</p>
<h3 class='section-title'>7. Fraud and Dishonest Activity</h3>
<ul class='policy-list'>
<li>The following activities are prohibited:</li>
</ul>
<p>Creating fake accounts.</p>
<p>Using false identities.</p>
<p>Manipulating fares.</p>
<p>Submitting false complaints.</p>
<p>Payment fraud.</p>
<p>Incentive abuse.</p>
<p>Account sharing.</p>
<p>Document forgery.</p>
<p>Fraudulent activity may result in permanent account termination and legal action.</p>
<h3 class='section-title'>8. Vehicle Care and Property Protection</h3>
<ul class='policy-list'>
<li>Passengers are expected to:</li>
</ul>
<p>Respect the driver’s vehicle.</p>
<p>Avoid damaging vehicle interiors or equipment.</p>
<p>Maintain cleanliness during trips.</p>
<ul class='policy-list'>
<li>Drivers are expected to:</li>
</ul>
<p>Maintain clean and safe vehicles.</p>
<p>Protect passenger belongings.</p>
<p>Handle lost-property reports responsibly.</p>
<p>Intentional property damage may lead to financial liability and account suspension.</p>
<h3 class='section-title'>9. Appropriate Communication</h3>
<p>Communication through the platform should remain professional and relevant to the trip.</p>
<ul class='policy-list'>
<li>Users must not:</li>
</ul>
<p>Send abusive messages.</p>
<p>Make threats.</p>
<p>Use obscene language.</p>
<p>Spam or repeatedly contact other users.</p>
<p>Share offensive content.</p>
<p>Communication channels are intended solely for transportation-related purposes.</p>
<h3 class='section-title'>10. Alcohol and Substance Abuse</h3>
<ul class='policy-list'>
<li>Drivers must never operate a vehicle while under the influence of:</li>
<li>Alcohol</li>
<li>Illegal drugs</li>
<li>Controlled substances that impair driving ability</li>
</ul>
<p>Passengers must not engage in behavior that creates safety risks due to intoxication or substance abuse.</p>
<p>Any report involving impaired driving will be investigated immediately.</p>
<h3 class='section-title'>11. Weapons and Dangerous Items</h3>
<p>Users must comply with applicable laws regarding weapons and dangerous items.</p>
<p>Doonspedo reserves the right to restrict transportation of any item that may threaten safety, including:</p>
<ul class='policy-list'>
<li>Explosives</li>
<li>Hazardous materials</li>
<li>Illegal weapons</li>
<li>Dangerous substances</li>
</ul>
<h3 class='section-title'>12. Account Security</h3>
<p>Users are responsible for protecting their accounts.</p>
<ul class='policy-list'>
<li>Do not:</li>
</ul>
<p>Share login credentials.</p>
<p>Allow unauthorized persons to use your account.</p>
<p>Create multiple accounts for fraudulent purposes.</p>
<p>Users should immediately report suspected unauthorized account access.</p>
<h3 class='section-title'>13. Ratings and Feedback</h3>
<p>Ratings and feedback help maintain service quality.</p>
<ul class='policy-list'>
<li>Users should:</li>
</ul>
<p>Provide honest reviews.</p>
<p>Avoid false reporting.</p>
<p>Report genuine safety concerns.</p>
<p>Abuse of the rating system may result in account action.</p>
<h3 class='section-title'>14. Reporting Concerns</h3>
<ul class='policy-list'>
<li>Users are encouraged to report:</li>
</ul>
<p>Safety incidents.</p>
<p>Harassment.</p>
<p>Fraud.</p>
<p>Discrimination.</p>
<p>Policy violations.</p>
<p>Suspicious activity.</p>
<p>Reports will be reviewed by the Doonspedo support team and handled in accordance with applicable policies.</p>
<h3 class='section-title'>15. Enforcement Actions</h3>
<ul class='policy-list'>
<li>Violations of these Community Guidelines may result in:</li>
</ul>
<p>Educational warnings.</p>
<p>Temporary account restrictions.</p>
<p>Ride access limitations.</p>
<p>Account suspension.</p>
<p>Permanent account deactivation.</p>
<p>Legal action where appropriate.</p>
<p>The severity of enforcement will depend on the nature and frequency of the violation.</p>
<h3 class='section-title'>16. Changes to Community Guidelines</h3>
<p>Doonspedo may update these Community Guidelines periodically to improve safety, compliance, and user experience.</p>
<p>Updated versions will be published through official channels, including the website and mobile applications.</p>
<p>Continued use of the platform after updates constitutes acceptance of the revised Guidelines.</p>
<h3 class='section-title'>17. Contact Us</h3>
<p>For questions, concerns, or reports related to Community Guidelines, please contact:</p>
<ul class='policy-list'>
<li>Doonspedo Support</li>
</ul>
<p>Email: <a href='mailto:support@doonspedo.com' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>support@doonspedo.com</a> <br> Phone: <a href='tel:+919627217655' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>+91 96272 17655</a> <br> Website: <a href='https://www.doonspedo.com' target='_blank' style='color: var(--primary-color, #cddc29); text-decoration: none; font-weight: bold;'>www.doonspedo.com</a></p>
<ul class='policy-list'>
<li>Our Commitment</li>
</ul>
<p>Doonspedo is committed to building a transportation community based on Safety, Respect, Integrity, and Trust. By working together, we can create a positive experience for every rider and driver on the platform.</p>
<p>Doonspedo – Safe Rides, Trusted Connections.</p>

                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
@endsection
