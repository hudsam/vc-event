<?php

return [
    'categories' => [
        ['name' => 'Technology', 'slug' => 'technology'],
        ['name' => 'Marketing', 'slug' => 'marketing'],
        ['name' => 'Business & Startup', 'slug' => 'business-startup'],
        ['name' => 'Design & Creative', 'slug' => 'design-creative'],
        ['name' => 'Health & Wellness', 'slug' => 'health-wellness'],
        ['name' => 'Finance & Investment', 'slug' => 'finance-investment'],
        ['name' => 'Self Improvement', 'slug' => 'self-improvement'],
        ['name' => 'Education', 'slug' => 'education'],
    ],

    'venues' => [
        ['name' => 'Jakarta Convention Center', 'city' => 'Jakarta'],
        ['name' => 'Online (Zoom)', 'city' => 'Online'],
        ['name' => 'Co-working Space Sudirman', 'city' => 'Jakarta'],
        ['name' => 'Grand City Hall Surabaya', 'city' => 'Surabaya'],
        ['name' => 'Bandung Creative Hub', 'city' => 'Bandung'],
        ['name' => 'Sleman City Hall', 'city' => 'Yogyakarta'],
    ],

    'organizers' => [
        ['name' => 'Maxy Academy'],
        ['name' => 'Tech Indonesia Community'],
        ['name' => 'Creative Hub Global'],
        ['name' => 'Startup Founders Club'],
    ],

    'speakers' => [
        [
            'name' => 'Budi Santoso',
            'title' => 'Lead AI Engineer at Tech Corp',
            'bio' => 'Budi is an expert in Machine Learning and Computer Vision with 10+ years of experience.',
            'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&q=80',
            'linkedin' => 'https://linkedin.com/in/budisantoso'
        ],
        [
            'name' => 'Siti Aminah',
            'title' => 'Digital Marketing Consultant',
            'bio' => 'Siti helps startups grow their organic and paid traffic using advanced SEO and growth hacking techniques.',
            'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=400&h=400&fit=crop&q=80',
            'linkedin' => 'https://linkedin.com/in/sitiaminah'
        ],
        [
            'name' => 'Andy Wijaya',
            'title' => 'Managing Partner at Venture Capital',
            'bio' => 'Andy has funded over 50+ early-stage startups in Southeast Asia.',
            'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&q=80',
            'linkedin' => 'https://linkedin.com/in/andywijaya'
        ],
        [
            'name' => 'Rina Putri',
            'title' => 'Senior UI/UX Designer',
            'bio' => 'Rina focuses on human-centered design systems and interactive interfaces.',
            'photo' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&q=80',
            'linkedin' => 'https://linkedin.com/in/rinaputri'
        ],
    ],

    'sponsors' => [
        [
            'name' => 'Google Cloud',
            'logo' => 'https://images.unsplash.com/photo-1573804633927-bfcbcd909acd?w=200&h=80&fit=crop&q=80',
            'website' => 'https://cloud.google.com',
            'tier' => 'gold'
        ],
        [
            'name' => 'Amazon Web Services',
            'logo' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=200&h=80&fit=crop&q=80',
            'website' => 'https://aws.amazon.com',
            'tier' => 'gold'
        ],
        [
            'name' => 'Midtrans',
            'logo' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=200&h=80&fit=crop&q=80',
            'website' => 'https://midtrans.com',
            'tier' => 'silver'
        ],
        [
            'name' => 'Niagahoster',
            'logo' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=200&h=80&fit=crop&q=80',
            'website' => 'https://niagahoster.co.id',
            'tier' => 'bronze'
        ],
        [
            'name' => 'Tech in Asia',
            'logo' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=200&h=80&fit=crop&q=80',
            'website' => 'https://techinasia.com',
            'tier' => 'media_partner'
        ],
    ],

    'schedules' => [
        [
            'title' => 'Opening Ceremony',
            'start_time' => '09:00',
            'end_time' => '09:30',
        ],
        [
            'title' => 'Keynote Speech: Future of AI',
            'start_time' => '09:30',
            'end_time' => '10:30',
        ],
        [
            'title' => 'Panel Discussion: Startup Fundraising',
            'start_time' => '10:45',
            'end_time' => '12:00',
        ],
        [
            'title' => 'Networking Lunch',
            'start_time' => '12:00',
            'end_time' => '13:00',
        ],
        [
            'title' => 'Workshop: Building Design Systems',
            'start_time' => '13:00',
            'end_time' => '15:00',
        ],
    ],

    'galleries' => [
        'https://images.unsplash.com/photo-1511578314322-379afb476865?w=600&h=400&fit=crop&q=80',
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop&q=80',
        'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=600&h=400&fit=crop&q=80',
        'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=600&h=400&fit=crop&q=80',
    ],

    'faqs' => [
        [
            'question' => 'Is this event open for beginners?',
            'answer' => 'Yes, all of our events are designed to be beginner-friendly while still providing valuable insights for professionals.'
        ],
        [
            'question' => 'Will I receive a certificate?',
            'answer' => 'Yes, all registered participants will receive an e-certificate of attendance within 3 days after the event.'
        ],
        [
            'question' => 'Can I get a refund if I cannot attend?',
            'answer' => 'Refunds are available up to 48 hours before the event starts. Please contact our support team.'
        ],
    ]
];
