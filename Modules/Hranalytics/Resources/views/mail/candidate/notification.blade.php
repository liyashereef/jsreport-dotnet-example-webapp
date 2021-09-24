@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<!-- header here -->
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}

Hello {{$candidate->name}},
<br>
<br>
Thank you for taking the time to complete your candidate profile. Attached is your application screen. I know you invested a great deal of time. In return, you have my commitment we will do our part to find an appropriate fit for you if you pass our candidate screen. At a minimum, we use algorithms to evaluate your experience, wage expectations, license renewal dates, distance to the job site, among 90 other criteria. As I stated in my recorded message, we are very selective of the applicants we invite to join our organization. However, when you get in – you have a world of opportunity and we take care of our guards.
I would ask you to be patient. Now that you are in our database, I or a member of our recruiting team will contact you if there is a good job match. If you don’t hear from us, it likely means there were a number of other applicants who were better suited for the position you applied for but don’t fret! You will still be considered for new jobs coming up each day.
<br>
Now, as the SVP/COO of Commissionaires Great Lakes, I have a company to run. However, if you don’t hear anything from us over the next 3 months, I will give you my email to contact me. I or my assistant will do our best to get back to you and let you know what is available. My personal email address is:
<br>
<a href="mailto:benjamin.alexander@commissionaires-cgl.ca">benjamin.alexander@commissionaires-cgl.ca</a>
<br>
Please do not abuse this. If you send me multiple emails or contact me within 3 months – I will have no choice but to block you. We have over 1,700 employees so you can appreciate my inbox often gets flooded. However, recruiting is so strategic to what we do that I do take time to get back to legitimate inquiries if you are looking to find out about the status of your application. Remember, give it a minimum of 3 months before you email me if you haven't been contacted and I'll inquire about the status of your application.
<br>
Regards,
<br>
Ben Alexander
SVP/COO
Commissionaires Great Lakes

@slot('subcopy')
@component('mail::subcopy')
<!-- subcopy here -->
@endcomponent
@endslot


{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<!-- footer here -->
&copy; {{ date('Y') }} CGL 360.

@endcomponent
@endslot
@endcomponent
