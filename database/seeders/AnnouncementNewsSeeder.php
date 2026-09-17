<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class AnnouncementNewsSeeder extends Seeder
{
    public function run(): void
    {
        $poster = User::where('email', 'superadmin@ilc.com')->first()
            ?? User::whereIn('role', ['superadmin', 'admin'])->orderBy('id')->first();

        if (!$poster) {
            $this->command?->warn('AnnouncementNewsSeeder: no admin/superadmin user found, skipping.');
            return;
        }

        $announcements = array (
  0 => 
  array (
    'title' => 'Enrollment for S.Y. 2027–2028 Now Open',
    'content' => 'IEMELIF Learning Center is now accepting new and returning student enrollment for School Year 2027–2028, from Nursery to Grade 6. Visit our Admission page or come to the registrar\'s office to begin your application. Slots are limited per grade level.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-06T18:41:44.000000Z',
  ),
  1 => 
  array (
    'title' => 'Early Enrollment Discount Extended',
    'content' => 'Good news! Due to popular demand, we are extending the early enrollment discount period by two weeks. Enroll now and enjoy reduced miscellaneous fees for the upcoming school year.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-18T13:53:27.000000Z',
  ),
  2 => 
  array (
    'title' => 'Requirements for New Student Enrollment',
    'content' => 'New students applying for enrollment must submit the following: PSA birth certificate, Form 138 (report card), Form 137 if transferring, 2 recent 2x2 photos, and a duly accomplished enrollment form. Incomplete requirements may delay processing.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-15T17:48:00.000000Z',
  ),
  3 => 
  array (
    'title' => 'Online Enrollment Portal Now Available',
    'content' => 'Parents and guardians may now start the enrollment process online through our website\'s Admission page. Simply fill out the application form and our registrar will contact you to complete the requirements and schedule your visit.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-14T15:19:54.000000Z',
  ),
  4 => 
  array (
    'title' => 'Enrollment Schedule for Returning Students',
    'content' => 'Returning students (Grades 1 to 6) may proceed with re-enrollment starting next week. Please bring your child\'s latest report card and settle any outstanding balance from the previous school year, if any.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-31T11:07:59.000000Z',
  ),
  5 => 
  array (
    'title' => 'Kindergarten Enrollment Orientation',
    'content' => 'Parents of incoming Kindergarten pupils are invited to attend a short orientation on enrollment requirements and the school\'s Kindergarten program. Please watch out for the schedule to be posted soon.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-18T06:45:51.000000Z',
  ),
  6 => 
  array (
    'title' => 'Transferee Enrollment Guidelines',
    'content' => 'Students transferring from another school must present their Form 137 (permanent record) and a certificate of good moral character from their previous school, in addition to the standard enrollment requirements.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-15T09:29:00.000000Z',
  ),
  7 => 
  array (
    'title' => 'Enrollment Deadline Reminder for Grade 6 Completers',
    'content' => 'Grade 6 completers planning to enroll in Junior High School elsewhere are reminded to request their school records early to avoid delays. Our registrar\'s office is ready to assist with document requests.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-09-10T01:07:39.000000Z',
  ),
  8 => 
  array (
    'title' => 'Limited Scholarship Slots Available',
    'content' => 'IEMELIF Learning Center offers limited scholarship slots for qualified students for the upcoming school year. Interested parents may inquire at the registrar\'s office for eligibility requirements and the application process.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-17T15:10:29.000000Z',
  ),
  9 => 
  array (
    'title' => 'Enrollment Payment Options Now Include GCash',
    'content' => 'For your convenience, tuition and fee payments during enrollment can now be made via cash at the cashier\'s counter or through GCash. Ask our cashier for the payment plan options available.',
    'category' => 'enrollment',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-09-14T21:22:32.000000Z',
  ),
  10 => 
  array (
    'title' => 'Complete School Uniform Required Starting Monday',
    'content' => 'This is a reminder to all students that the complete school uniform, including proper PE attire on scheduled days, is required starting next week. Students without complete uniform may be asked to secure a gate pass from the Guidance Office.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-27T19:08:08.000000Z',
  ),
  11 => 
  array (
    'title' => 'Bring Your Own Water Bottle',
    'content' => 'Students are encouraged to bring their own labeled water bottles to school daily to stay hydrated and to help reduce single-use plastic waste on campus.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-03T18:58:08.000000Z',
  ),
  12 => 
  array (
    'title' => 'School ID Must Be Worn at All Times',
    'content' => 'All students are reminded to wear their school ID at all times while inside the campus. This helps our guards and staff quickly identify and assist our students.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-23T11:41:38.000000Z',
  ),
  13 => 
  array (
    'title' => 'Proper Disposal of Trash Reminder',
    'content' => 'Let us all do our part in keeping our school clean. Please dispose of trash properly in the designated bins and observe basic waste segregation.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-26T01:16:18.000000Z',
  ),
  14 => 
  array (
    'title' => 'Reminder on Arrival Time',
    'content' => 'Classes begin promptly at 7:30 AM. Students are reminded to arrive at least 15 minutes before class starts. Repeated tardiness will be reported to parents through the Guidance Office.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-17T13:20:41.000000Z',
  ),
  15 => 
  array (
    'title' => 'No Gadgets Allowed During Class Hours',
    'content' => 'Students are reminded that personal gadgets such as mobile phones and tablets should remain switched off and kept away during class hours unless a teacher permits their use for a learning activity.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-30T10:30:42.000000Z',
  ),
  16 => 
  array (
    'title' => 'Report Card Signing Deadline',
    'content' => 'Parents and guardians are reminded to sign and return their child\'s report card within one week of distribution as proof of review.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-22T17:00:41.000000Z',
  ),
  17 => 
  array (
    'title' => 'School Clinic Hours Reminder',
    'content' => 'The school clinic is open from 7:00 AM to 4:00 PM on school days. Students needing medical attention should be accompanied by a classmate or teacher to the clinic.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-24T05:57:08.000000Z',
  ),
  18 => 
  array (
    'title' => 'Please Update Your Contact Information',
    'content' => 'Parents and guardians are requested to update their contact numbers and address with the registrar\'s office to ensure the school can reach you promptly in case of emergencies.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-15T10:27:24.000000Z',
  ),
  19 => 
  array (
    'title' => 'Library Book Return Reminder',
    'content' => 'Students who currently have borrowed books from the school library are reminded to return them on or before the end of the month to avoid holding fees on report card release.',
    'category' => 'reminder',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-24T02:32:58.000000Z',
  ),
  20 => 
  array (
    'title' => 'Foundation Day Celebration This Friday',
    'content' => 'Join us this Friday as we celebrate IEMELIF Learning Center\'s Foundation Day! The program starts at 8:00 AM in the school covered court, featuring student performances, awarding of achievers, and games. Parents are welcome to attend.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-25T23:07:05.000000Z',
  ),
  21 => 
  array (
    'title' => 'Buwan ng Wika Celebration',
    'content' => 'In celebration of Buwan ng Wika this August, students are encouraged to wear Filipiniana-inspired attire every Friday. A cultural program showcasing Filipino literature, songs, and dances will be held on the last week of the month.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-15T22:22:53.000000Z',
  ),
  22 => 
  array (
    'title' => 'Intramurals Week Schedule',
    'content' => 'Get ready for a week of friendly competition! Intramurals will feature sports events, cheer dance, and mini-games for all grade levels. Detailed schedules per grade level will be posted at the bulletin board.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-23T04:40:46.000000Z',
  ),
  23 => 
  array (
    'title' => 'Nutrition Month Activities',
    'content' => 'In observance of Nutrition Month this July, the school will hold a "Healthy Baon" contest, feeding program updates, and a short program on proper nutrition for pupils.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-29T20:51:47.000000Z',
  ),
  24 => 
  array (
    'title' => 'Christmas Program and Party',
    'content' => 'Mark your calendars! Our annual Christmas Program and Class Parties will be held before the holiday break. Details on the class party guidelines will be sent through your child\'s adviser.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-15T18:37:52.000000Z',
  ),
  25 => 
  array (
    'title' => 'Field Trip for Grade 5 and 6 Students',
    'content' => 'Grade 5 and 6 students are invited to join an educational field trip to a local museum and heritage site. Permission forms and fees will be distributed by the class advisers.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-20T07:52:20.000000Z',
  ),
  26 => 
  array (
    'title' => 'Recognition Day for Academic Achievers',
    'content' => 'The school will hold its quarterly Recognition Day to honor students with academic excellence, good conduct, and perfect attendance. Parents of awardees are highly encouraged to attend.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-30T12:17:55.000000Z',
  ),
  27 => 
  array (
    'title' => 'Fire and Earthquake Drill Schedule',
    'content' => 'As part of our commitment to student safety, a scheduled fire and earthquake drill will be conducted this month. Teachers will orient students on evacuation procedures beforehand.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-30T06:39:59.000000Z',
  ),
  28 => 
  array (
    'title' => 'Career Day for Grade 6 Students',
    'content' => 'Grade 6 students will participate in a Career Day activity featuring guest speakers from various professions to inspire pupils as they prepare for junior high school.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-30T20:36:04.000000Z',
  ),
  29 => 
  array (
    'title' => 'Family Day and Mini Sports Fest',
    'content' => 'Families are invited to join our Family Day and Mini Sports Fest, a fun day of games and activities designed to strengthen the bond between the school and home.',
    'category' => 'activity',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-15T21:04:40.000000Z',
  ),
  30 => 
  array (
    'title' => 'First Quarter Examination Schedule',
    'content' => 'First quarter examinations will be held next week. Please see the posted schedule per grade level at the bulletin board. Students are reminded to review their notes and get enough rest before each exam day.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-08T08:02:27.000000Z',
  ),
  31 => 
  array (
    'title' => 'Release of Report Cards',
    'content' => 'First quarter report cards will be released and distributed by class advisers. Parents are requested to personally claim and sign their child\'s report card on the scheduled date.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-18T14:45:00.000000Z',
  ),
  32 => 
  array (
    'title' => 'Remedial Classes for Struggling Learners',
    'content' => 'Remedial reading and numeracy sessions are now available after class hours for students who need additional support. Interested parents may coordinate with their child\'s adviser.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-09-10T16:12:20.000000Z',
  ),
  33 => 
  array (
    'title' => 'Reading Program Assessment Schedule',
    'content' => 'The school\'s reading assessment for all grade levels will be conducted this month as part of our ongoing effort to monitor and improve every pupil\'s reading level.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-09-01T15:49:32.000000Z',
  ),
  34 => 
  array (
    'title' => 'Guidelines on Class Suspension Announcements',
    'content' => 'In case of inclement weather or other emergencies, class suspension announcements will be posted on this website and our official social media page as early as possible.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-05T03:20:57.000000Z',
  ),
  35 => 
  array (
    'title' => 'Summer Class Enrollment for Grade Repeaters',
    'content' => 'Students who need to retake a subject may enroll in our summer remedial classes. Please coordinate with the Guidance Office for scheduling and requirements.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-16T13:39:01.000000Z',
  ),
  36 => 
  array (
    'title' => 'Homeroom Assignment for the New School Year',
    'content' => 'Homeroom and adviser assignments for the upcoming school year will be posted at the bulletin board and announced through this website once finalized.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-17T06:40:05.000000Z',
  ),
  37 => 
  array (
    'title' => 'Weekly Learning Activity Schedule',
    'content' => 'Teachers will be distributing the weekly schedule of learning activities and topics per subject to help parents guide their children at home.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-23T06:27:45.000000Z',
  ),
  38 => 
  array (
    'title' => 'National Achievement Test Reminder',
    'content' => 'Grade 6 students are reminded of the upcoming National Achievement Test. Reviewers and practice materials will be provided by subject teachers in the coming weeks.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-04-21T07:11:32.000000Z',
  ),
  39 => 
  array (
    'title' => 'Honor Roll List Now Posted',
    'content' => 'Congratulations to our first quarter honor students! The complete honor roll list per grade level is now posted at the bulletin board and will be announced during the flag ceremony.',
    'category' => 'academic',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-04-27T13:25:49.000000Z',
  ),
  40 => 
  array (
    'title' => 'School Clinic Now Open Daily',
    'content' => 'Our school clinic is now open daily during school hours to attend to minor injuries and health concerns of our students, staffed by our school nurse.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-07-14T01:07:37.000000Z',
  ),
  41 => 
  array (
    'title' => 'New Reading Corner Opens',
    'content' => 'A new reading corner has been set up at the elementary building, giving pupils a dedicated space to explore storybooks and reading materials during free time.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-04T18:00:03.000000Z',
  ),
  42 => 
  array (
    'title' => 'School Canteen Menu Update',
    'content' => 'Our school canteen has updated its menu to include more affordable and nutritious meal options for our students. Price list is posted at the canteen area.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-05T15:30:02.000000Z',
  ),
  43 => 
  array (
    'title' => 'Adjusted School Hours Notice',
    'content' => 'Please be informed of a temporary adjustment to school hours this week due to a scheduled facility maintenance. Classes will resume regular hours the following week.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-15T12:13:30.000000Z',
  ),
  44 => 
  array (
    'title' => 'Lost and Found Reminder',
    'content' => 'Parents and students are reminded to check the Lost and Found area at the Guidance Office for any missing personal belongings, especially water bottles, jackets, and school supplies.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-09T00:28:49.000000Z',
  ),
  45 => 
  array (
    'title' => 'Computer Laboratory Now Available for Student Use',
    'content' => 'Our computer laboratory is now available for scheduled use by classes as part of the ICT curriculum, as well as for research purposes during free periods.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-05-15T23:42:27.000000Z',
  ),
  46 => 
  array (
    'title' => 'Feeding Program for Identified Pupils',
    'content' => 'The school\'s feeding program for identified underweight pupils continues this quarter, in partnership with the Parent-Teacher Association and local health office.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-09T10:19:58.000000Z',
  ),
  47 => 
  array (
    'title' => 'School Facility Improvements Underway',
    'content' => 'Minor repainting and repair works are currently ongoing at several classrooms to ensure a safe and pleasant learning environment for all students.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-19T07:54:06.000000Z',
  ),
  48 => 
  array (
    'title' => 'New School Website and Online Portal',
    'content' => 'We are excited to announce our new school website, where you can view announcements, news, and access student, teacher, and finance portals online.',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-06-01T09:43:26.000000Z',
  ),
  49 => 
  array (
    'title' => 'Holiday Break Schedule',
    'content' => 'Please be informed of the upcoming holiday break schedule. Classes will resume on the date indicated in the school calendar. Have a safe and restful break!',
    'category' => 'general',
    'audience' => 'all',
    'is_active' => true,
    'created_at' => '2026-08-13T03:54:21.000000Z',
  ),
);

        $news = array (
  0 => 
  array (
    'title' => 'IEMELIF Learners Shine in Division Reading Assessment',
    'body' => 'Students of IEMELIF Learning Center demonstrated strong performance in the recent Division-wide reading assessment, with several pupils recognized as outstanding readers in their grade level. The school continues to strengthen its reading program through daily reading time and remedial sessions.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-07-27T18:27:38.000000Z',
  ),
  1 => 
  array (
    'title' => 'School Adopts New Curriculum Enhancement Program',
    'body' => 'IEMELIF Learning Center has begun implementing a curriculum enhancement program aimed at strengthening foundational literacy and numeracy skills among Nursery to Grade 3 pupils, in line with national learning standards.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-09-02T17:20:04.000000Z',
  ),
  2 => 
  array (
    'title' => 'Teachers Complete In-Service Training',
    'body' => 'Our teaching staff recently completed an in-service training focused on modern, learner-centered teaching strategies, equipping them with fresh approaches to engage students in the classroom.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-09-15T13:44:26.000000Z',
  ),
  3 => 
  array (
    'title' => 'Grade 6 Pupils Prepare for Junior High Transition',
    'body' => 'The school has begun orientation sessions for Grade 6 pupils to help ease their transition into Junior High School, covering topics such as study habits, time management, and choosing a school.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-06-17T15:18:09.000000Z',
  ),
  4 => 
  array (
    'title' => 'School Strengthens Remedial Reading Program',
    'body' => 'In response to assessment results, IEMELIF Learning Center has expanded its remedial reading sessions to give more individualized attention to learners who need extra support.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-08-08T23:57:08.000000Z',
  ),
  5 => 
  array (
    'title' => 'IEMELIF Learning Center Recognized for Academic Performance',
    'body' => 'The school received recognition from local education officials for its consistent academic performance and initiatives supporting student learning over the past school year.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-06-18T22:09:53.000000Z',
  ),
  6 => 
  array (
    'title' => 'New Library Resources Boost Student Learning',
    'body' => 'The school library recently received an addition of new storybooks and reference materials, giving pupils more resources to support their reading and research activities.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-04-30T12:18:15.000000Z',
  ),
  7 => 
  array (
    'title' => 'School Partners with Local Organizations for Mentorship',
    'body' => 'IEMELIF Learning Center has formed a partnership with local community organizations to provide mentorship and enrichment activities for its upper elementary pupils.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-06-21T21:15:45.000000Z',
  ),
  8 => 
  array (
    'title' => 'Teachers Attend Seminar on Inclusive Education',
    'body' => 'Selected teachers attended a seminar on inclusive education practices, aimed at better supporting learners with diverse needs within the regular classroom setting.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-05-18T14:34:09.000000Z',
  ),
  9 => 
  array (
    'title' => 'Grade Level Coordinators Introduce New Assessment Tools',
    'body' => 'The school\'s grade level coordinators have rolled out new formative assessment tools to help teachers better track individual student progress throughout the quarter.',
    'category' => 'academic',
    'is_active' => true,
    'created_at' => '2026-07-14T00:07:39.000000Z',
  ),
  10 => 
  array (
    'title' => 'School Celebrates Successful Foundation Day Program',
    'body' => 'IEMELIF Learning Center marked another milestone with a joyful Foundation Day celebration attended by students, parents, and guests. The event featured cultural presentations, recognition of outstanding students, and games for the whole school community.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-03-31T09:15:34.000000Z',
  ),
  11 => 
  array (
    'title' => 'IEMELIF Holds Annual Recognition Day',
    'body' => 'Students who excelled academically and in conduct were honored during the school\'s annual Recognition Day, held with proud parents and guardians in attendance.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-09-04T06:13:30.000000Z',
  ),
  12 => 
  array (
    'title' => 'School Hosts Christmas Program for Pupils and Parents',
    'body' => 'The school community came together for a festive Christmas program featuring student performances, a gift-giving activity, and a shared meal among families.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-09-01T03:33:00.000000Z',
  ),
  13 => 
  array (
    'title' => 'Intramurals Week Concludes with Exciting Finals',
    'body' => 'This year\'s Intramurals Week wrapped up with thrilling championship matches and a lively closing program, capping off a week of sportsmanship and school spirit.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-05-04T05:16:41.000000Z',
  ),
  14 => 
  array (
    'title' => 'School Welcomes New Batch of Kindergarten Learners',
    'body' => 'IEMELIF Learning Center welcomed its newest batch of Kindergarten pupils with a simple orientation program for both children and their parents.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-05-20T03:12:39.000000Z',
  ),
  15 => 
  array (
    'title' => 'Graduation Ceremony Honors Grade 6 Completers',
    'body' => 'The school held a heartfelt graduation ceremony for its Grade 6 completers, celebrating their years of growth and preparing them for the next chapter of their education.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-06-19T22:45:25.000000Z',
  ),
  16 => 
  array (
    'title' => 'School Marks Nutrition Month with Health Fair',
    'body' => 'A mini health fair was held on campus in observance of Nutrition Month, featuring a healthy food fair, height and weight monitoring, and a nutrition awareness program.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-05-17T16:02:01.000000Z',
  ),
  17 => 
  array (
    'title' => 'Teachers\' Day Celebration Honors School Educators',
    'body' => 'Students and staff came together to celebrate Teachers\' Day with a short appreciation program honoring the dedication of IEMELIF Learning Center\'s teaching staff.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-07-10T09:44:59.000000Z',
  ),
  18 => 
  array (
    'title' => 'School Holds Family Day and Fun Activities',
    'body' => 'Families gathered on campus for a Family Day filled with games, food, and bonding activities, strengthening the partnership between home and school.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-06-02T02:58:38.000000Z',
  ),
  19 => 
  array (
    'title' => 'Buwan ng Wika Culminating Activity a Success',
    'body' => 'The school\'s month-long celebration of Filipino language and culture culminated in a vibrant program featuring student performances in traditional costumes.',
    'category' => 'events',
    'is_active' => true,
    'created_at' => '2026-04-07T10:59:27.000000Z',
  ),
  20 => 
  array (
    'title' => 'Brigada Eskwela: Community Comes Together for School Clean-Up',
    'body' => 'Parents, teachers, and local volunteers joined hands for this year\'s Brigada Eskwela, helping repair classrooms, clean school grounds, and prepare facilities ahead of the school year. The school extends its gratitude to all who participated.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-03-28T09:57:27.000000Z',
  ),
  21 => 
  array (
    'title' => 'Students Join Tree Planting Activity',
    'body' => 'Pupils and teachers participated in a tree planting activity within the school grounds as part of the school\'s environmental awareness program.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-31T16:53:25.000000Z',
  ),
  22 => 
  array (
    'title' => 'Pupils Participate in Healthy Baon Cook-Off',
    'body' => 'As part of Nutrition Month, students showcased creative and healthy packed meals in a friendly "Healthy Baon" contest judged by teachers and the school nurse.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-06-03T09:41:15.000000Z',
  ),
  23 => 
  array (
    'title' => 'School Conducts Earthquake and Fire Drill',
    'body' => 'IEMELIF Learning Center successfully conducted its scheduled earthquake and fire drill, reinforcing safety awareness and proper evacuation procedures among students and staff.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-09T21:17:49.000000Z',
  ),
  24 => 
  array (
    'title' => 'Grade 5 and 6 Students Enjoy Educational Field Trip',
    'body' => 'Grade 5 and 6 pupils had an enriching day exploring a local museum and heritage site as part of their social studies enrichment activity.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-06-14T00:22:07.000000Z',
  ),
  25 => 
  array (
    'title' => 'Students Showcase Talents in Foundation Day Program',
    'body' => 'Pupils from different grade levels displayed their talents in singing, dancing, and drama during the school\'s Foundation Day cultural presentation.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-04-28T22:54:47.000000Z',
  ),
  26 => 
  array (
    'title' => 'School Organizes Blood Donation Awareness Drive',
    'body' => 'In partnership with a local health office, the school organized an awareness activity on blood donation for its teaching and parent community.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-11T07:10:33.000000Z',
  ),
  27 => 
  array (
    'title' => 'Pupils Join Community Clean-Up Drive',
    'body' => 'Students and teachers participated in a community clean-up initiative near the school, promoting environmental responsibility beyond the campus.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-12T23:36:36.000000Z',
  ),
  28 => 
  array (
    'title' => 'School Holds Career Day for Grade 6 Pupils',
    'body' => 'Guest speakers from various professions visited the school to share their career journeys and inspire Grade 6 pupils as they look toward junior high school.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-05T15:59:57.000000Z',
  ),
  29 => 
  array (
    'title' => 'Students Participate in Local Quiz Bee',
    'body' => 'A team of IEMELIF Learning Center pupils represented the school in a local inter-school quiz bee, showcasing their knowledge across various subjects.',
    'category' => 'activity',
    'is_active' => true,
    'created_at' => '2026-07-25T10:57:44.000000Z',
  ),
  30 => 
  array (
    'title' => 'Grade 5 Pupil Wins Local Essay Writing Contest',
    'body' => 'A Grade 5 pupil from IEMELIF Learning Center brought pride to the school after winning first place in a local essay writing contest, impressing judges with a well-written piece on family values.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-05-12T13:20:27.000000Z',
  ),
  31 => 
  array (
    'title' => 'IEMELIF Learning Center Bags Award in Quiz Bee',
    'body' => 'The school\'s quiz bee team earned recognition after placing among the top schools in a local inter-school academic competition.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-04-08T22:31:43.000000Z',
  ),
  32 => 
  array (
    'title' => 'School Recognized for Clean and Green Campus',
    'body' => 'IEMELIF Learning Center received a commendation from local officials for maintaining a clean, green, and well-organized school campus.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-09-13T19:47:42.000000Z',
  ),
  33 => 
  array (
    'title' => 'Pupil Represents School in Science Fair',
    'body' => 'A young science enthusiast from Grade 6 represented IEMELIF Learning Center in a local science fair, presenting a project on simple renewable energy concepts.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-07-21T17:08:40.000000Z',
  ),
  34 => 
  array (
    'title' => 'Grade 6 Class Achieves High Promotion Rate',
    'body' => 'The school\'s outgoing Grade 6 batch achieved a high promotion rate this school year, a testament to the dedication of both students and teachers.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-05-11T11:47:12.000000Z',
  ),
  35 => 
  array (
    'title' => 'Teacher Recognized as Outstanding Educator',
    'body' => 'One of IEMELIF Learning Center\'s teachers was recognized as an outstanding educator by a local teachers\' association for dedication and innovative teaching practices.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-05-13T02:51:00.000000Z',
  ),
  36 => 
  array (
    'title' => 'School\'s Reading Program Cited as Good Practice',
    'body' => 'The school\'s reading intervention program was cited as a good practice by local education officials during a recent school visit.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-08-09T17:34:43.000000Z',
  ),
  37 => 
  array (
    'title' => 'Young Athletes Bring Home Medals from Sports Meet',
    'body' => 'IEMELIF Learning Center\'s young athletes competed well in a local sports meet, bringing home several medals in track and field events.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-09-01T23:14:36.000000Z',
  ),
  38 => 
  array (
    'title' => 'School Choir Wins Local Singing Competition',
    'body' => 'The school\'s student choir earned first place in a local inter-school singing competition, delighting the audience with a heartfelt performance.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-07-27T07:34:39.000000Z',
  ),
  39 => 
  array (
    'title' => 'Pupils Earn Certificates in Arts Competition',
    'body' => 'Several IEMELIF Learning Center pupils received certificates of recognition in a local arts and poster-making competition themed around environmental protection.',
    'category' => 'achievement',
    'is_active' => true,
    'created_at' => '2026-04-18T01:02:10.000000Z',
  ),
  40 => 
  array (
    'title' => 'New Reading Corner Opens for Elementary Pupils',
    'body' => 'A new reading corner has been set up at the elementary building, giving pupils a dedicated space to explore storybooks and reading materials during free time. The initiative supports the school\'s ongoing push to build strong literacy habits early.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-04-28T23:28:45.000000Z',
  ),
  41 => 
  array (
    'title' => 'School Canteen Introduces Healthier Meal Options',
    'body' => 'IEMELIF Learning Center\'s canteen has updated its offerings to include more nutritious and affordable meal choices for students throughout the school day.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-07-27T15:00:45.000000Z',
  ),
  42 => 
  array (
    'title' => 'IEMELIF Learning Center Launches New Website',
    'body' => 'The school has officially launched its new website and online portal system, allowing students, parents, and teachers to access announcements, news, and school services more conveniently.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-06-21T09:36:06.000000Z',
  ),
  43 => 
  array (
    'title' => 'School Facilities Get Fresh Coat of Paint',
    'body' => 'Several classrooms and common areas received a fresh coat of paint ahead of the new school year, part of the school\'s ongoing facility improvement efforts.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-08-16T01:33:06.000000Z',
  ),
  44 => 
  array (
    'title' => 'School Clinic Expands Services for Pupils',
    'body' => 'The school clinic has expanded its daily services, now offering basic health monitoring alongside its usual first-aid care for students.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-04-08T16:00:40.000000Z',
  ),
  45 => 
  array (
    'title' => 'New Computer Laboratory Now Open',
    'body' => 'IEMELIF Learning Center opened a new computer laboratory equipped with additional units to support the school\'s ICT curriculum and student research needs.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-08-24T18:36:40.000000Z',
  ),
  46 => 
  array (
    'title' => 'School Announces Updated Safety Protocols',
    'body' => 'The school has rolled out updated campus safety protocols, including visitor sign-in procedures and clearer emergency evacuation routes, for the safety of all students and staff.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-08-01T19:55:46.000000Z',
  ),
  47 => 
  array (
    'title' => 'School Improves Internet Connectivity for Learning',
    'body' => 'IEMELIF Learning Center has upgraded its internet connectivity to better support online learning tools and administrative systems used by teachers and staff.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-08-18T00:34:52.000000Z',
  ),
  48 => 
  array (
    'title' => 'School Grounds Beautification Project Completed',
    'body' => 'A school-wide beautification project, including new plant boxes and improved walkways, has been completed with the help of parent volunteers.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-07-21T13:15:08.000000Z',
  ),
  49 => 
  array (
    'title' => 'New Bulletin Board System Keeps Parents Informed',
    'body' => 'The school has set up an improved bulletin board display near the main entrance to keep parents and visitors updated on the latest announcements and events at a glance.',
    'category' => 'general',
    'is_active' => true,
    'created_at' => '2026-09-14T02:56:26.000000Z',
  ),
);

        if (DB::table('announcements')->where('title', 'Enrollment for S.Y. 2027–2028 Now Open')->exists()) {
            $this->command?->info('AnnouncementNewsSeeder: announcements already seeded, skipping.');
        } else {
            foreach ($announcements as $row) {
                DB::table('announcements')->insert([
                    'teacher_id' => $poster->id,
                    'title'      => $row['title'],
                    'content'    => $row['content'],
                    'category'   => $row['category'],
                    'audience'   => $row['audience'],
                    'is_active'  => $row['is_active'],
                    'created_at' => Carbon::parse($row['created_at']),
                    'updated_at' => Carbon::parse($row['created_at']),
                ]);
            }
            $this->command?->info('AnnouncementNewsSeeder: inserted ' . count($announcements) . ' announcements.');
        }

        if (DB::table('news')->where('title', 'IEMELIF Learners Shine in Division Reading Assessment')->exists()) {
            $this->command?->info('AnnouncementNewsSeeder: news already seeded, skipping.');
        } else {
            foreach ($news as $row) {
                DB::table('news')->insert([
                    'posted_by'  => $poster->id,
                    'title'      => $row['title'],
                    'body'       => $row['body'],
                    'category'   => $row['category'],
                    'is_active'  => $row['is_active'],
                    'created_at' => Carbon::parse($row['created_at']),
                    'updated_at' => Carbon::parse($row['created_at']),
                ]);
            }
            $this->command?->info('AnnouncementNewsSeeder: inserted ' . count($news) . ' news articles.');
        }
    }
}
