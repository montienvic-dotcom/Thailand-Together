<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JourneySeeder extends Seeder
{
    public function run(): void
    {
        $clusterId = DB::table('clusters')->where('code', 'PTY')->value('id');

        $journeys = [
            // ── Group A: Restaurants / Street Food ──
            ['code' => 'A1', 'group_code' => 'A', 'name_en' => 'Food Route – Local to Mall', 'name_th' => 'เส้นทางอาหาร – ท้องถิ่นสู่ห้าง', 'stops' => 5, 'duration_min' => 525, 'est_spend_thb' => 1640, 'tp_n' => 96, 'tp_g' => 145, 'tp_s' => 262, 'tone' => 'lively enjoyable, strong revenue distribution'],
            ['code' => 'A2', 'group_code' => 'A', 'name_en' => 'Seafood Route – Naklua to Jomtien', 'name_th' => 'เส้นทางซีฟู้ด – นาเกลือถึงจอมเทียน', 'stops' => 4, 'duration_min' => 445, 'est_spend_thb' => 2380, 'tp_n' => 125, 'tp_g' => 183, 'tp_s' => 320, 'tone' => 'local fully satisfying family-friendly'],
            ['code' => 'A3', 'group_code' => 'A', 'name_en' => 'Day-To-Night Romantic Dining', 'name_th' => 'เส้นทางโรแมนติก – กลางวันสู่กลางคืน', 'stops' => 3, 'duration_min' => 405, 'est_spend_thb' => 1930, 'tp_n' => 107, 'tp_g' => 160, 'tp_s' => 284, 'tone' => 'Romantic, scenic, photogenic'],
            ['code' => 'A4', 'group_code' => 'A', 'name_en' => 'Family Food Trail – Market to Mall', 'name_th' => 'เส้นทางอาหารครอบครัว – ตลาดสู่ห้าง', 'stops' => 5, 'duration_min' => 595, 'est_spend_thb' => 2150, 'tp_n' => 106, 'tp_g' => 152, 'tp_s' => 252, 'tone' => 'fun, comfortable, and great value for the whole family'],
            ['code' => 'A5', 'group_code' => 'A', 'name_en' => 'Budget Street Food Trail', 'name_th' => 'เส้นทางอาหารริมทาง – ประหยัด', 'stops' => 5, 'duration_min' => 470, 'est_spend_thb' => 930, 'tp_n' => 67, 'tp_g' => 109, 'tp_s' => 204, 'tone' => 'value-oriented, flexible, and easy to enjoy'],
            ['code' => 'A6', 'group_code' => 'A', 'name_en' => 'Premium Dining Journey', 'name_th' => 'เส้นทางอาหารพรีเมียม', 'stops' => 3, 'duration_min' => 375, 'est_spend_thb' => 3250, 'tp_n' => 150, 'tp_g' => 209, 'tp_s' => 340, 'tone' => 'premium, high-image, and easy to close'],
            ['code' => 'A7', 'group_code' => 'A', 'name_en' => 'Multicultural Food Route', 'name_th' => 'เส้นทางอาหารพหุวัฒนธรรม', 'stops' => 4, 'duration_min' => 400, 'est_spend_thb' => 1700, 'tp_n' => 88, 'tp_g' => 127, 'tp_s' => 216, 'tone' => 'Multicultural-friendly and accessible'],
            ['code' => 'A8', 'group_code' => 'A', 'name_en' => 'Cafe & Dessert Photo Route', 'name_th' => 'เส้นทางคาเฟ่ & ขนม ถ่ายรูป', 'stops' => 4, 'duration_min' => 380, 'est_spend_thb' => 1350, 'tp_n' => 84, 'tp_g' => 130, 'tp_s' => 238, 'tone' => 'light, photogenic, and easy to share'],
            ['code' => 'A9', 'group_code' => 'A', 'name_en' => 'Night Market Food Trail', 'name_th' => 'เส้นทางอาหารตลาดกลางคืน', 'stops' => 4, 'duration_min' => 440, 'est_spend_thb' => 1300, 'tp_n' => 72, 'tp_g' => 107, 'tp_s' => 184, 'tone' => 'lively enjoyable, strong revenue distribution'],
            ['code' => 'A10', 'group_code' => 'A', 'name_en' => 'Terminal 21 Food & Mall Route', 'name_th' => 'เส้นทางอาหาร Terminal 21', 'stops' => 3, 'duration_min' => 420, 'est_spend_thb' => 1000, 'tp_n' => 80, 'tp_g' => 131, 'tp_s' => 260, 'tone' => 'lively enjoyable, strong revenue distribution'],

            // ── Group B: Hotels / Accommodation ──
            ['code' => 'B1', 'group_code' => 'B', 'name_en' => 'Work & Stay – Hotel + Co-Working', 'name_th' => 'ทำงาน & พัก – โรงแรม + Co-Working', 'stops' => 5, 'duration_min' => 660, 'est_spend_thb' => 3530, 'tp_n' => 161, 'tp_g' => 222, 'tp_s' => 362, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'B2', 'group_code' => 'B', 'name_en' => 'Family Resort Day Out', 'name_th' => 'ครอบครัว รีสอร์ท เดย์ เอาท์', 'stops' => 4, 'duration_min' => 635, 'est_spend_thb' => 2950, 'tp_n' => 148, 'tp_g' => 214, 'tp_s' => 366, 'tone' => 'comfortable, easy to use, and family-friendly'],
            ['code' => 'B3', 'group_code' => 'B', 'name_en' => 'Luxury Staycation & Spa', 'name_th' => 'Luxury Staycation & สปา', 'stops' => 4, 'duration_min' => 655, 'est_spend_thb' => 8150, 'tp_n' => 346, 'tp_g' => 464, 'tp_s' => 732, 'tone' => 'Premium, high-image, high-spending'],
            ['code' => 'B4', 'group_code' => 'B', 'name_en' => 'Budget Hotel + City Explore', 'name_th' => 'โรงแรมประหยัด + เที่ยวเมือง', 'stops' => 4, 'duration_min' => 630, 'est_spend_thb' => 1800, 'tp_n' => 102, 'tp_g' => 153, 'tp_s' => 274, 'tone' => 'value-driven, flexible, and easy to continue after the stay'],
            ['code' => 'B5', 'group_code' => 'B', 'name_en' => 'Mid-Range + Spa & Wellness', 'name_th' => 'โรงแรมกลาง + สปา & เวลเนส', 'stops' => 3, 'duration_min' => 490, 'est_spend_thb' => 2930, 'tp_n' => 147, 'tp_g' => 212, 'tp_s' => 364, 'tone' => 'relaxed, balanced, and quality-time oriented'],
            ['code' => 'B6', 'group_code' => 'B', 'name_en' => 'Stay & Shop – Hotel + Malls', 'name_th' => 'พัก & ช้อป – โรงแรม + ห้าง', 'stops' => 4, 'duration_min' => 555, 'est_spend_thb' => 2850, 'tp_n' => 134, 'tp_g' => 187, 'tp_s' => 308, 'tone' => 'comfortable stay with easy add-on extension'],
            ['code' => 'B7', 'group_code' => 'B', 'name_en' => 'Luxury Hotel Full Experience', 'name_th' => 'โรงแรมหรู ประสบการณ์เต็มรูปแบบ', 'stops' => 4, 'duration_min' => 710, 'est_spend_thb' => 8150, 'tp_n' => 356, 'tp_g' => 484, 'tp_s' => 782, 'tone' => 'Premium, high-image, high-spending'],
            ['code' => 'B8', 'group_code' => 'B', 'name_en' => 'Business Hotel + MICE', 'name_th' => 'โรงแรมธุรกิจ + MICE', 'stops' => 6, 'duration_min' => 770, 'est_spend_thb' => 4200, 'tp_n' => 188, 'tp_g' => 257, 'tp_s' => 416, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'B9', 'group_code' => 'B', 'name_en' => 'Family Hotel + Fun Activities', 'name_th' => 'โรงแรมครอบครัว + กิจกรรม', 'stops' => 5, 'duration_min' => 730, 'est_spend_thb' => 3150, 'tp_n' => 146, 'tp_g' => 203, 'tp_s' => 332, 'tone' => 'comfortable, easy to use, and family-friendly'],
            ['code' => 'B10', 'group_code' => 'B', 'name_en' => 'Hotel + City Pass Full Day', 'name_th' => 'โรงแรม + City Pass เต็มวัน', 'stops' => 6, 'duration_min' => 725, 'est_spend_thb' => 4650, 'tp_n' => 206, 'tp_g' => 281, 'tp_s' => 452, 'tone' => 'Premium, high-image, high-spending'],

            // ── Group C: Entertainment / Nightlife ──
            ['code' => 'C1', 'group_code' => 'C', 'name_en' => 'Dinner & Show Night', 'name_th' => 'อาหารเย็น & โชว์', 'stops' => 4, 'duration_min' => 405, 'est_spend_thb' => 2300, 'tp_n' => 112, 'tp_g' => 159, 'tp_s' => 264, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C2', 'group_code' => 'C', 'name_en' => 'Premium Cabaret & Rooftop', 'name_th' => 'คาบาเร่ต์พรีเมียม & รูฟท็อป', 'stops' => 3, 'duration_min' => 325, 'est_spend_thb' => 3500, 'tp_n' => 160, 'tp_g' => 222, 'tp_s' => 360, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C3', 'group_code' => 'C', 'name_en' => 'Walking Street Photo Walk', 'name_th' => 'วอล์คกิ้งสตรีท ถ่ายรูป', 'stops' => 2, 'duration_min' => 315, 'est_spend_thb' => 550, 'tp_n' => 62, 'tp_g' => 108, 'tp_s' => 224, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C4', 'group_code' => 'C', 'name_en' => 'Mall + Live Music Night', 'name_th' => 'ห้าง + ไลฟ์มิวสิคไนท์', 'stops' => 4, 'duration_min' => 445, 'est_spend_thb' => 1530, 'tp_n' => 81, 'tp_g' => 119, 'tp_s' => 202, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C5', 'group_code' => 'C', 'name_en' => 'Arcade + Bowling + Show', 'name_th' => 'อาร์เคด + โบว์ลิ่ง + โชว์', 'stops' => 4, 'duration_min' => 480, 'est_spend_thb' => 2700, 'tp_n' => 128, 'tp_g' => 180, 'tp_s' => 296, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C6', 'group_code' => 'C', 'name_en' => 'Spa + Dinner + Show', 'name_th' => 'สปา + อาหารเย็น + โชว์', 'stops' => 3, 'duration_min' => 340, 'est_spend_thb' => 2650, 'tp_n' => 126, 'tp_g' => 177, 'tp_s' => 292, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C7', 'group_code' => 'C', 'name_en' => 'Bowling + Arcade Night', 'name_th' => 'โบว์ลิ่ง + อาร์เคดไนท์', 'stops' => 3, 'duration_min' => 430, 'est_spend_thb' => 1250, 'tp_n' => 70, 'tp_g' => 105, 'tp_s' => 180, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C8', 'group_code' => 'C', 'name_en' => 'Night Market Triple Tour', 'name_th' => 'ทัวร์ตลาดกลางคืนสามแห่ง', 'stops' => 4, 'duration_min' => 500, 'est_spend_thb' => 1400, 'tp_n' => 66, 'tp_g' => 93, 'tp_s' => 162, 'tone' => 'lively high-energy, but controlled safe'],
            ['code' => 'C9', 'group_code' => 'C', 'name_en' => 'Sunset Yacht + Premium Dining', 'name_th' => 'เรือยอชท์พระอาทิตย์ตก + อาหารพรีเมียม', 'stops' => 3, 'duration_min' => 450, 'est_spend_thb' => 6550, 'tp_n' => 282, 'tp_g' => 381, 'tp_s' => 604, 'tone' => 'premium, visually striking, and high-spending'],
            ['code' => 'C10', 'group_code' => 'C', 'name_en' => 'Full Day Tour + Night Show', 'name_th' => 'ทัวร์เต็มวัน + โชว์กลางคืน', 'stops' => 4, 'duration_min' => 930, 'est_spend_thb' => 3550, 'tp_n' => 162, 'tp_g' => 224, 'tp_s' => 364, 'tone' => 'lively high-energy, but controlled safe'],

            // ── Group D: Spa / Wellness ──
            ['code' => 'D1', 'group_code' => 'D', 'name_en' => 'Morning Wellness Reset', 'name_th' => 'เวลเนสตอนเช้า', 'stops' => 3, 'duration_min' => 330, 'est_spend_thb' => 1550, 'tp_n' => 92, 'tp_g' => 141, 'tp_s' => 254, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D2', 'group_code' => 'D', 'name_en' => 'Premium Spa & Detox Day', 'name_th' => 'สปาพรีเมียม & ดีท็อกซ์', 'stops' => 3, 'duration_min' => 345, 'est_spend_thb' => 4800, 'tp_n' => 212, 'tp_g' => 290, 'tp_s' => 464, 'tone' => 'premium, relaxing, and health-connected'],
            ['code' => 'D3', 'group_code' => 'D', 'name_en' => 'Active Yoga & Massage', 'name_th' => 'โยคะ & นวด', 'stops' => 3, 'duration_min' => 370, 'est_spend_thb' => 1250, 'tp_n' => 80, 'tp_g' => 125, 'tp_s' => 230, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D4', 'group_code' => 'D', 'name_en' => 'Health Check + Spa Package', 'name_th' => 'ตรวจสุขภาพ + สปา', 'stops' => 3, 'duration_min' => 345, 'est_spend_thb' => 4950, 'tp_n' => 218, 'tp_g' => 297, 'tp_s' => 476, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D5', 'group_code' => 'D', 'name_en' => 'Family Fun + Wellness', 'name_th' => 'ครอบครัว + เวลเนส', 'stops' => 3, 'duration_min' => 380, 'est_spend_thb' => 1050, 'tp_n' => 72, 'tp_g' => 114, 'tp_s' => 214, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D6', 'group_code' => 'D', 'name_en' => 'Cafe & Spa Afternoon', 'name_th' => 'คาเฟ่ & สปาบ่าย', 'stops' => 3, 'duration_min' => 220, 'est_spend_thb' => 1250, 'tp_n' => 80, 'tp_g' => 125, 'tp_s' => 230, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D7', 'group_code' => 'D', 'name_en' => 'Premium Detox & Rooftop Dining', 'name_th' => 'ดีท็อกซ์ & อาหารรูฟท็อป', 'stops' => 3, 'duration_min' => 345, 'est_spend_thb' => 4800, 'tp_n' => 212, 'tp_g' => 290, 'tp_s' => 464, 'tone' => 'premium, relaxing, and health-connected'],
            ['code' => 'D8', 'group_code' => 'D', 'name_en' => 'Meditation & Clean Food', 'name_th' => 'นั่งสมาธิ & อาหารสะอาด', 'stops' => 3, 'duration_min' => 310, 'est_spend_thb' => 750, 'tp_n' => 60, 'tp_g' => 99, 'tp_s' => 190, 'tone' => 'relaxing, restorative, and balanced'],
            ['code' => 'D9', 'group_code' => 'D', 'name_en' => 'Fitness Bootcamp & Recovery', 'name_th' => 'ฟิตเนส & ฟื้นฟูร่างกาย', 'stops' => 4, 'duration_min' => 330, 'est_spend_thb' => 2150, 'tp_n' => 106, 'tp_g' => 152, 'tp_s' => 252, 'tone' => 'Relaxing, restorative, balanced'],
            ['code' => 'D10', 'group_code' => 'D', 'name_en' => 'Spa + Dinner + Show Night', 'name_th' => 'สปา + อาหาร + โชว์', 'stops' => 3, 'duration_min' => 330, 'est_spend_thb' => 2650, 'tp_n' => 126, 'tp_g' => 177, 'tp_s' => 292, 'tone' => 'Relaxing, restorative, balanced'],

            // ── Group E: Transport / Tours / Travel ──
            ['code' => 'E1', 'group_code' => 'E', 'name_en' => 'Night Market + Seafood Walk', 'name_th' => 'ตลาดกลางคืน + ซีฟู้ดวอล์ค', 'stops' => 2, 'duration_min' => 395, 'est_spend_thb' => 1000, 'tp_n' => 80, 'tp_g' => 132, 'tp_s' => 260, 'tone' => 'Agile, practical, easy to book'],
            ['code' => 'E2', 'group_code' => 'E', 'name_en' => 'Koh Larn Island Hop', 'name_th' => 'เกาะล้าน ไอส์แลนด์ฮอป', 'stops' => 3, 'duration_min' => 580, 'est_spend_thb' => 1600, 'tp_n' => 94, 'tp_g' => 143, 'tp_s' => 258, 'tone' => 'City-opening, fresh, high-movement'],
            ['code' => 'E3', 'group_code' => 'E', 'name_en' => 'Water Sports & Dining', 'name_th' => 'กีฬาทางน้ำ & อาหาร', 'stops' => 2, 'duration_min' => 325, 'est_spend_thb' => 2450, 'tp_n' => 128, 'tp_g' => 187, 'tp_s' => 326, 'tone' => 'City-opening, fresh, high-movement'],
            ['code' => 'E4', 'group_code' => 'E', 'name_en' => 'Low-Carbon City Bike', 'name_th' => 'จักรยานเมือง Low-Carbon', 'stops' => 1, 'duration_min' => 310, 'est_spend_thb' => 600, 'tp_n' => 64, 'tp_g' => 111, 'tp_s' => 228, 'tone' => 'simple, sustainable, and story-driven'],
            ['code' => 'E5', 'group_code' => 'E', 'name_en' => 'Koh Larn Family + Arcade', 'name_th' => 'เกาะล้านครอบครัว + อาร์เคด', 'stops' => 3, 'duration_min' => 515, 'est_spend_thb' => 1250, 'tp_n' => 80, 'tp_g' => 125, 'tp_s' => 230, 'tone' => 'agile, practical, and easy to book'],
            ['code' => 'E6', 'group_code' => 'E', 'name_en' => 'Full-Day Van + Nong Nooch', 'name_th' => 'รถตู้เต็มวัน + สวนนงนุช', 'stops' => 3, 'duration_min' => 790, 'est_spend_thb' => 2000, 'tp_n' => 110, 'tp_g' => 164, 'tp_s' => 290, 'tone' => 'Agile, practical, easy to book'],
            ['code' => 'E7', 'group_code' => 'E', 'name_en' => 'Coastal Seafood Tour', 'name_th' => 'ทัวร์ซีฟู้ดชายฝั่ง', 'stops' => 3, 'duration_min' => 650, 'est_spend_thb' => 2500, 'tp_n' => 120, 'tp_g' => 169, 'tp_s' => 280, 'tone' => 'Agile, practical, easy to book'],
            ['code' => 'E8', 'group_code' => 'E', 'name_en' => 'Scooter City Explorer', 'name_th' => 'มอเตอร์ไซค์สำรวจเมือง', 'stops' => 4, 'duration_min' => 380, 'est_spend_thb' => 1550, 'tp_n' => 92, 'tp_g' => 140, 'tp_s' => 254, 'tone' => 'Agile, practical, easy to book'],
            ['code' => 'E9', 'group_code' => 'E', 'name_en' => 'Premium Yacht Full Day', 'name_th' => 'เรือยอชท์เต็มวัน พรีเมียม', 'stops' => 3, 'duration_min' => 665, 'est_spend_thb' => 10650, 'tp_n' => 446, 'tp_g' => 594, 'tp_s' => 932, 'tone' => 'City-opening, fresh, high-movement'],
            ['code' => 'E10', 'group_code' => 'E', 'name_en' => 'Koh Larn Bike & Ferry', 'name_th' => 'เกาะล้าน จักรยาน & เรือ', 'stops' => 2, 'duration_min' => 470, 'est_spend_thb' => 250, 'tp_n' => 50, 'tp_g' => 93, 'tp_s' => 200, 'tone' => 'Agile, practical, easy to book'],

            // ── Group F: Events / MICE / Conferences ──
            ['code' => 'F1', 'group_code' => 'F', 'name_en' => 'Conference + City Experience', 'name_th' => 'งานประชุม + ประสบการณ์เมือง', 'stops' => 4, 'duration_min' => 570, 'est_spend_thb' => 3150, 'tp_n' => 146, 'tp_g' => 204, 'tp_s' => 332, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'F2', 'group_code' => 'F', 'name_en' => 'Exhibition + Shopping Day', 'name_th' => 'นิทรรศการ + วันช้อปปิ้ง', 'stops' => 4, 'duration_min' => 420, 'est_spend_thb' => 1900, 'tp_n' => 86, 'tp_g' => 119, 'tp_s' => 202, 'tone' => 'High-energy, crowd-building, UGC-driven'],
            ['code' => 'F3', 'group_code' => 'F', 'name_en' => 'Sports Event + City Tour', 'name_th' => 'กีฬา + ทัวร์เมือง', 'stops' => 4, 'duration_min' => 525, 'est_spend_thb' => 1650, 'tp_n' => 86, 'tp_g' => 125, 'tp_s' => 212, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'F4', 'group_code' => 'F', 'name_en' => 'Festival Night Market Walk', 'name_th' => 'เทศกาล + ตลาดกลางคืน', 'stops' => 3, 'duration_min' => 545, 'est_spend_thb' => 1000, 'tp_n' => 70, 'tp_g' => 112, 'tp_s' => 210, 'tone' => 'High-energy, crowd-building, UGC-driven'],
            ['code' => 'F5', 'group_code' => 'F', 'name_en' => 'Sports + Wellness Recovery', 'name_th' => 'กีฬา + ฟื้นฟูเวลเนส', 'stops' => 3, 'duration_min' => 300, 'est_spend_thb' => 1500, 'tp_n' => 90, 'tp_g' => 139, 'tp_s' => 250, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'F6', 'group_code' => 'F', 'name_en' => 'Workshop + Dinner + Show', 'name_th' => 'เวิร์คช้อป + อาหารเย็น + โชว์', 'stops' => 3, 'duration_min' => 430, 'est_spend_thb' => 2650, 'tp_n' => 126, 'tp_g' => 177, 'tp_s' => 292, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'F7', 'group_code' => 'F', 'name_en' => 'MICE Full Day + Gala', 'name_th' => 'MICE เต็มวัน + กาล่า', 'stops' => 4, 'duration_min' => 555, 'est_spend_thb' => 3850, 'tp_n' => 174, 'tp_g' => 240, 'tp_s' => 388, 'tone' => 'High-energy, crowd-building, UGC-driven'],
            ['code' => 'F8', 'group_code' => 'F', 'name_en' => 'Scenic Dining & Event', 'name_th' => 'อาหารวิวสวย & อีเว้นท์', 'stops' => 2, 'duration_min' => 360, 'est_spend_thb' => 1800, 'tp_n' => 112, 'tp_g' => 174, 'tp_s' => 324, 'tone' => 'High-energy, crowd-building, UGC-driven'],
            ['code' => 'F9', 'group_code' => 'F', 'name_en' => 'Concert + Dinner Night', 'name_th' => 'คอนเสิร์ต + อาหารเย็น', 'stops' => 3, 'duration_min' => 405, 'est_spend_thb' => 2250, 'tp_n' => 130, 'tp_g' => 197, 'tp_s' => 340, 'tone' => 'Professional, agile, conversion-focused'],
            ['code' => 'F10', 'group_code' => 'F', 'name_en' => 'Conference + Gala Dinner Premium', 'name_th' => 'ประชุม + กาล่าดินเนอร์ พรีเมียม', 'stops' => 3, 'duration_min' => 600, 'est_spend_thb' => 4800, 'tp_n' => 212, 'tp_g' => 290, 'tp_s' => 464, 'tone' => 'Professional, agile, conversion-focused'],

            // ── Group G: Shopping / Souvenirs / Lifestyle ──
            ['code' => 'G1', 'group_code' => 'G', 'name_en' => 'Show & Beach Walk', 'name_th' => 'โชว์ & เดินริมหาด', 'stops' => 2, 'duration_min' => 450, 'est_spend_thb' => 2000, 'tp_n' => 88, 'tp_g' => 142, 'tp_s' => 276, 'tone' => 'content content-rich, dynamic, highly shareable'],
            ['code' => 'G2', 'group_code' => 'G', 'name_en' => 'Mall Hopping + Cafe', 'name_th' => 'ช้อปห้าง + คาเฟ่', 'stops' => 3, 'duration_min' => 465, 'est_spend_thb' => 1800, 'tp_n' => 72, 'tp_g' => 115, 'tp_s' => 214, 'tone' => 'Easy to walk, repeat-purchase friendly, revenue-distributing'],
            ['code' => 'G3', 'group_code' => 'G', 'name_en' => 'Art & Culture Explorer', 'name_th' => 'ศิลปะ & วัฒนธรรม', 'stops' => 3, 'duration_min' => 495, 'est_spend_thb' => 2800, 'tp_n' => 94, 'tp_g' => 143, 'tp_s' => 258, 'tone' => 'Story-rich, visually strong, culturally valuable'],
            ['code' => 'G4', 'group_code' => 'G', 'name_en' => 'Night Market & Street Walk', 'name_th' => 'ตลาดกลางคืน & เดินสตรีท', 'stops' => 3, 'duration_min' => 475, 'est_spend_thb' => 1850, 'tp_n' => 68, 'tp_g' => 110, 'tp_s' => 206, 'tone' => 'Local, satisfying, and easy to extend'],
            ['code' => 'G5', 'group_code' => 'G', 'name_en' => 'Mall + Show + Market Triple', 'name_th' => 'ห้าง + โชว์ + ตลาด', 'stops' => 3, 'duration_min' => 465, 'est_spend_thb' => 2350, 'tp_n' => 104, 'tp_g' => 156, 'tp_s' => 278, 'tone' => 'content content-rich, dynamic, highly shareable'],
            ['code' => 'G6', 'group_code' => 'G', 'name_en' => 'Ice Museum + Cafe + Show', 'name_th' => 'พิพิธภัณฑ์น้ำแข็ง + คาเฟ่ + โชว์', 'stops' => 3, 'duration_min' => 475, 'est_spend_thb' => 2650, 'tp_n' => 116, 'tp_g' => 171, 'tp_s' => 302, 'tone' => 'Fun, comfortable, easy to photograph'],
            ['code' => 'G7', 'group_code' => 'G', 'name_en' => 'Central Shopping + Sunset', 'name_th' => 'ช้อปเซ็นทรัล + พระอาทิตย์ตก', 'stops' => 3, 'duration_min' => 450, 'est_spend_thb' => 1300, 'tp_n' => 72, 'tp_g' => 115, 'tp_s' => 214, 'tone' => 'Light, balanced, and easy to share'],
            ['code' => 'G8', 'group_code' => 'G', 'name_en' => 'Marina + Terminal 21 + Local', 'name_th' => 'มารีน่า + Terminal 21 + ท้องถิ่น', 'stops' => 3, 'duration_min' => 390, 'est_spend_thb' => 950, 'tp_n' => 54, 'tp_g' => 92, 'tp_s' => 178, 'tone' => 'content content-rich, dynamic, highly shareable'],
            ['code' => 'G9', 'group_code' => 'G', 'name_en' => 'Sanctuary + Market + Cafe', 'name_th' => 'ปราสาทสัจธรรม + ตลาด + คาเฟ่', 'stops' => 3, 'duration_min' => 490, 'est_spend_thb' => 2200, 'tp_n' => 88, 'tp_g' => 136, 'tp_s' => 246, 'tone' => 'content content-rich, dynamic, highly shareable'],
            ['code' => 'G10', 'group_code' => 'G', 'name_en' => 'Heritage + Market + Cafe', 'name_th' => 'มรดก + ตลาด + คาเฟ่', 'stops' => 3, 'duration_min' => 520, 'est_spend_thb' => 1750, 'tp_n' => 100, 'tp_g' => 151, 'tp_s' => 270, 'tone' => 'Story-rich, visually strong, culturally valuable'],

            // ── Group H: Attractions / Activities / Family ──
            ['code' => 'H1', 'group_code' => 'H', 'name_en' => 'Floating Market + Mall Full Day', 'name_th' => 'ตลาดน้ำ + ห้างเต็มวัน', 'stops' => 3, 'duration_min' => 655, 'est_spend_thb' => 3050, 'tp_n' => 72, 'tp_g' => 115, 'tp_s' => 214, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H2', 'group_code' => 'H', 'name_en' => 'Nong Nooch + Viewpoint + Cafe', 'name_th' => 'สวนนงนุช + จุดชมวิว + คาเฟ่', 'stops' => 3, 'duration_min' => 715, 'est_spend_thb' => 2150, 'tp_n' => 72, 'tp_g' => 114, 'tp_s' => 214, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H3', 'group_code' => 'H', 'name_en' => 'Art In Paradise + Mall + Market', 'name_th' => 'Art In Paradise + ห้าง + ตลาด', 'stops' => 3, 'duration_min' => 590, 'est_spend_thb' => 2450, 'tp_n' => 72, 'tp_g' => 115, 'tp_s' => 214, 'tone' => 'Story-rich, visually strong, culturally valuable'],
            ['code' => 'H4', 'group_code' => 'H', 'name_en' => 'Art + Cafe + Night Market', 'name_th' => 'ศิลปะ + คาเฟ่ + ตลาดกลางคืน', 'stops' => 3, 'duration_min' => 440, 'est_spend_thb' => 1400, 'tp_n' => 74, 'tp_g' => 117, 'tp_s' => 218, 'tone' => 'Easy to walk, repeat-purchase friendly, revenue-distributing'],
            ['code' => 'H5', 'group_code' => 'H', 'name_en' => 'Nong Nooch + Cafe + Terminal 21', 'name_th' => 'สวนนงนุช + คาเฟ่ + Terminal 21', 'stops' => 3, 'duration_min' => 705, 'est_spend_thb' => 2550, 'tp_n' => 84, 'tp_g' => 130, 'tp_s' => 238, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H6', 'group_code' => 'H', 'name_en' => 'Sanctuary + Central + Terminal', 'name_th' => 'ปราสาทสัจธรรม + เซ็นทรัล + Terminal', 'stops' => 3, 'duration_min' => 540, 'est_spend_thb' => 2700, 'tp_n' => 90, 'tp_g' => 139, 'tp_s' => 250, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H7', 'group_code' => 'H', 'name_en' => 'Frost Ice + Cafe + Terminal 21', 'name_th' => 'Frost Ice + คาเฟ่ + Terminal 21', 'stops' => 3, 'duration_min' => 675, 'est_spend_thb' => 2650, 'tp_n' => 84, 'tp_g' => 130, 'tp_s' => 238, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H8', 'group_code' => 'H', 'name_en' => 'Beach Walk + Cafe + Market', 'name_th' => 'เดินหาด + คาเฟ่ + ตลาด', 'stops' => 3, 'duration_min' => 575, 'est_spend_thb' => 2400, 'tp_n' => 54, 'tp_g' => 91, 'tp_s' => 178, 'tone' => 'fun comfortable family-friendly'],
            ['code' => 'H9', 'group_code' => 'H', 'name_en' => 'Floating Market + Terminal + Viewpoint', 'name_th' => 'ตลาดน้ำ + Terminal + จุดชมวิว', 'stops' => 3, 'duration_min' => 560, 'est_spend_thb' => 1950, 'tp_n' => 62, 'tp_g' => 102, 'tp_s' => 194, 'tone' => 'content-rich easy to use suitable for short-stay'],
            ['code' => 'H10', 'group_code' => 'H', 'name_en' => 'Floating Market + Terminal + Central', 'name_th' => 'ตลาดน้ำ + Terminal + เซ็นทรัล', 'stops' => 3, 'duration_min' => 690, 'est_spend_thb' => 2800, 'tp_n' => 74, 'tp_g' => 118, 'tp_s' => 218, 'tone' => 'fun comfortable family-friendly'],
        ];

        $groupNames = [
            'A' => ['en' => 'Restaurants / Street Food', 'th' => 'ร้านอาหาร / สตรีทฟู้ด'],
            'B' => ['en' => 'Hotels / Accommodation', 'th' => 'โรงแรม / ที่พัก'],
            'C' => ['en' => 'Entertainment / Nightlife', 'th' => 'เอ็นเตอร์เทนเมนท์ / ไนท์ไลฟ์'],
            'D' => ['en' => 'Spa / Health / Wellness', 'th' => 'สปา / สุขภาพ / เวลเนส'],
            'E' => ['en' => 'Transport / Tours / Travel', 'th' => 'ขนส่ง / ทัวร์ / ท่องเที่ยว'],
            'F' => ['en' => 'Events / MICE / Conferences', 'th' => 'อีเว้นท์ / MICE / ประชุม'],
            'G' => ['en' => 'Shopping / Souvenirs / Lifestyle', 'th' => 'ช้อปปิ้ง / ของฝาก / ไลฟ์สไตล์'],
            'H' => ['en' => 'Attractions / Activities / Family', 'th' => 'สถานที่ท่องเที่ยว / กิจกรรม / ครอบครัว'],
        ];

        foreach ($journeys as $j) {
            $journeyId = DB::table('journey')->updateOrInsert(
                ['journey_code' => $j['code'], 'cluster_id' => $clusterId],
                [
                    'group_code'       => $j['group_code'],
                    'group_name_th'    => $groupNames[$j['group_code']]['th'],
                    'group_name_en'    => $groupNames[$j['group_code']]['en'],
                    'journey_name_th'  => $j['name_th'],
                    'journey_name_en'  => $j['name_en'],
                    'main_stops'       => $j['stops'],
                    'duration_min'     => $j['duration_min'],
                    'est_spend_thb'    => $j['est_spend_thb'],
                    'tp_normal'        => $j['tp_n'],
                    'tp_goal'          => $j['tp_g'],
                    'tp_special'       => $j['tp_s'],
                    'tone'             => $j['tone'],
                    'is_active'        => true,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]
            );
        }
    }
}
