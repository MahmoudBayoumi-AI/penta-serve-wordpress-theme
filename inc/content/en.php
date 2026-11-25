<?php
/**
 * English page content (blocks) — single source of truth from the Final Website Development Brief.
 *
 * @package penta-serve
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = array(
	array(
		'title'        => 'Project Assessment & Development',
		'text'         => 'Assessing project requirements in terms of the nature of the products, required temperature ranges, storage capacities, project location and operational needs, in order to prepare a clear concept that helps the investor make informed decisions before implementation begins.',
		'slot'         => 'service_1',
		'slot_variant' => 'service',
	),
	array(
		'title'        => 'Design & Planning',
		'text'         => 'Preparing and coordinating technical designs and layouts for refrigeration and freezing warehouses, including space planning, loading and unloading areas and internal movement arrangements, taking into account operational requirements and optimal use of space.',
		'slot'         => 'service_2',
		'slot_variant' => 'service',
	),
	array(
		'title'        => 'Thermal Insulation & Insulated Panel Solutions',
		'text'         => 'Coordinating the supply and installation of insulated panels (Sandwich Panels) and cold room and freezer doors, according to project requirements, target temperature ranges, and insulation and thermal efficiency standards.',
		'slot'         => 'service_3',
		'slot_variant' => 'service',
	),
	array(
		'title'        => 'Refrigeration & Freezing Systems',
		'text'         => 'Identifying and coordinating the supply and installation of suitable industrial refrigeration and freezing systems, according to storage capacities, required temperature ranges, the nature of the products, operating requirements and energy efficiency.',
		'slot'         => 'service_4',
		'slot_variant' => 'service',
	),
	array(
		'title'        => 'Operational Processes',
		'text'         => 'Planning and organizing daily operational activities, monitoring refrigeration and storage systems, and organizing procedures for receiving, storing and handling goods, in line with operational requirements and product safety.',
		'slot'         => 'service_5',
		'slot_variant' => 'service',
	),
	array(
		'title'        => 'Management & Supervision',
		'text'         => 'Providing management, follow-up and operational supervision services, preparing reports and monitoring performance indicators, to support operational efficiency, project continuity and effective management.',
		'slot'         => 'service_6',
		'slot_variant' => 'service',
	),
);

$solutions = array(
	array(
		'title' => 'Commercial Cold Storage Warehouses',
		'text'  => 'Commercial refrigerated storage facilities designed around the required temperatures, storage capacities and operational requirements.',
		'slot'  => 'sector_1',
	),
	array(
		'title' => 'Freezer Warehouses',
		'text'  => 'Frozen storage facilities operating at low temperatures, designed around preservation requirements, thermal insulation and operating efficiency.',
		'slot'  => 'sector_2',
	),
	array(
		'title' => 'Chilled & Frozen Food Storage Warehouses',
		'text'  => 'Storage solutions that take into account the preservation and safety requirements of chilled and frozen food products within an organized storage environment.',
		'slot'  => 'sector_3',
	),
	array(
		'title' => 'Cold Storage Facilities',
		'text'  => 'Integrated cold storage facilities including cold rooms and freezers, loading and unloading areas and internal movement arrangements.',
		'slot'  => 'sector_4',
	),
);

$why = array(
	'Specialized professional experience in managing refrigeration and freezing warehouses.',
	'Understanding of Saudi market requirements and food storage needs.',
	'Experience and relationships with local and international suppliers.',
	'Specialized knowledge of the requirements for importing and storing chilled and frozen food and cold supply chains.',
	'Integrated solutions designed around the needs of each project.',
	'Coordination of technical, commercial and operational services.',
	'Clear definition of the scope of services and contractual responsibilities.',
);

$stats = array(
	'<strong>40+</strong> Over 40 years of specialized professional experience in the refrigeration and freezing sector and warehouse management',
	'<strong>6</strong> Integrated service areas, from assessment to supervision',
	'<strong>Integrated Solutions</strong>',
);

/* ------------------------------------------------------------------ HOME */
$home  = ps_blk_group(
	ps_blk_eyebrow( 'Invest in<br>Kingdom of Saudi Arabia With<br>PENTA SERVE' ) .
	ps_blk_heading( 'We Build Investment Opportunities in Refrigerated and Frozen Storage', 1 ) .
	ps_blk_p( 'Your Gateway to Investing in a Promising Sector Supporting Saudi Vision 2030', 'ps-hero__tagline' ) .
	ps_blk_p( 'We help investors develop commercial refrigeration and freezing warehouse projects, from initial assessment, planning and design, through coordination of equipment and facilities, to operations and management, according to the needs of each project.' ) .
	ps_blk_buttons( array(
		array( 'label' => 'Explore Our Investment Solutions', 'url' => '{url:services}#investment', 'style' => 'primary' ),
		array( 'label' => 'Contact Us', 'url' => '{url:contact}', 'style' => 'light' ),
	) ),
	'ps-hero'
);

$home .= ps_blk_section(
	ps_blk_eyebrow( 'About Us' ) .
	ps_blk_heading( 'Your Partner in Developing Refrigerated and Frozen Storage Projects', 2 ) .
	ps_blk_p( 'PENTA SERVE specializes in developing commercial refrigeration and freezing warehouse solutions and projects, providing integrated services to investors, from initial project assessment, planning and design, through coordinating the supply of equipment and facilities, to operations, management and supervision.' ) .
	ps_blk_p( 'Our services are based on specialized professional experience in managing refrigeration and freezing warehouses and an understanding of the requirements for storing chilled and frozen food products, in addition to experience in frozen food imports and professional relationships with local and international suppliers, and knowledge of market requirements and cold supply chains.' ) .
	"<!-- Verification note: The \"More than 40 years of combined practical and leadership experience within the team\" claim is subject to verification and documentation. -->\n" .
	ps_blk_list( $stats, 'ps-stats' ) .
	ps_blk_buttons( array( array( 'label' => 'About Us', 'url' => '{url:about}', 'style' => 'outline' ) ) )
);

$home .= ps_blk_section(
	ps_blk_video_split(
		ps_blk_eyebrow( 'PENTA SERVE KSA' ) .
		ps_blk_heading( 'Watch Our Introduction Video', 2 ) .
		ps_blk_p( 'Discover the concept of commercial refrigeration and freezing warehouses, their role in preserving food products and supporting supply chain continuity, and how PENTA SERVE helps investors transform their ideas into fully developed projects.' ) .
		ps_blk_buttons( array( array( 'label' => 'Contact Us', 'url' => '{url:contact}', 'style' => 'primary' ) ) )
	),
	'soft video'
);

$home .= ps_blk_section(
	ps_blk_eyebrow( 'Our Services' ) .
	ps_blk_heading( 'Our Services', 2 ) .
	ps_blk_cards( $services, 3, true, 'ps-grid--services' ) .
	ps_blk_eyebrow( 'Our Solution Areas' ) .
	ps_blk_heading( 'Our Solution Areas', 2 ) .
	ps_blk_cards( $solutions, 2, false, 'ps-grid--solutions' ) .
	ps_blk_buttons( array( array( 'label' => 'Services & Investment Opportunities', 'url' => '{url:services}', 'style' => 'outline' ) ) )
);

$home .= ps_blk_section(
	ps_blk_eyebrow( 'Why PENTA SERVE?' ) .
	ps_blk_heading( 'One Partner from Concept to Operation', 2 ) .
	ps_blk_list( $why, 'ps-checks' ),
	'partner'
);

$home .= ps_blk_section(
	ps_blk_heading( 'Do You Have a Project Under Consideration?', 2 ) .
	ps_blk_p( 'Let us help you assess your idea and develop your project in the refrigeration and freezing warehouse sector.' ) .
	ps_blk_buttons( array(
		array( 'label' => 'Discuss Your Project With Us', 'url' => '{url:contact}', 'style' => 'primary' ),
	) ),
	'cta'
);

/* ----------------------------------------------------------------- ABOUT */
$about = ps_blk_section(
	ps_blk_eyebrow( 'Company Overview' ) .
	ps_blk_heading( 'Company Overview', 2 ) .
	ps_blk_p( 'PENTA SERVE specializes in developing commercial refrigeration and freezing warehouse solutions and projects, providing integrated services to investors, from initial project assessment, planning and design, through coordinating the supply of equipment and facilities, to operations, management and supervision.' ) .
	ps_blk_p( 'Our services are based on specialized professional experience in managing refrigeration and freezing warehouses and an understanding of the requirements for storing chilled and frozen food products, in addition to experience in frozen food imports and professional relationships with local and international suppliers, and knowledge of market requirements and cold supply chains.' )
);

$about .= ps_blk_section(
	ps_blk_cards( array(
		array(
			'title' => 'Vision',
			'text'  => 'To become a trusted partner for investors in developing refrigerated and frozen storage projects across Saudi Arabia, through integrated solutions that combine commercial understanding, technical expertise and professional operational management.',
		),
		array(
			'title' => 'Mission',
			'text'  => 'To help investors assess and develop refrigerated and frozen storage projects by providing integrated services that include project assessment, design, equipment coordination, implementation coordination, operations and management, according to each project\'s requirements and a clearly defined contractual scope.',
		),
	), 2, false ),
	'soft'
);

$about .= ps_blk_section(
	ps_blk_eyebrow( 'Objectives' ) .
	ps_blk_heading( 'Objectives', 2 ) .
	ps_blk_list( array(
		'Provide integrated solutions for developing refrigerated and frozen storage projects in line with market requirements.',
		'Help investors make informed decisions based on clear project assessments.',
		'Benefit from the team\'s expertise in managing and developing chilled and frozen storage businesses.',
		'Ensure clear service scopes and responsibilities for all parties.',
		'Support project continuity and long-term operational efficiency and management.',
	), 'ps-checks' )
);

$about .= ps_blk_section(
	ps_blk_eyebrow( 'Team Expertise' ) .
	ps_blk_heading( 'Team Expertise', 2 ) .
	ps_blk_p( 'Team members have more than 40 years of specialized professional experience in the field.' ) .
	ps_blk_list( array(
		'Management and business development.',
		'Experience in importing frozen food products and working with local and international suppliers.',
		'Understanding the local market, chilled and frozen food storage requirements and cold supply chains.',
	), 'ps-checks' ) .
	ps_blk_p( 'Important: These qualifications represent the professional experience of the team members, and not previous projects, achievements or completed contracts of PENTA SERVE.', 'ps-scope-note' ),
	'soft'
);

$about .= ps_blk_section(
	ps_blk_heading( 'Do You Have a Project Under Consideration?', 2 ) .
	ps_blk_p( 'Let us help you assess your idea and develop your project in the refrigeration and freezing warehouse sector.' ) .
	ps_blk_buttons( array(
		array( 'label' => 'Discuss Your Project With Us', 'url' => '{url:contact}', 'style' => 'primary' ),
	) ),
	'cta'
);

/* ---------------------------------- SERVICES & INVESTMENT OPPORTUNITIES */
$services_page = ps_blk_shortcode( '[ps_services_subnav]' );

// Section 1: Our Services (#services).
$services_page .= ps_blk_section(
	ps_blk_eyebrow( 'Our Services' ) .
	ps_blk_heading( 'Our Services', 2 ) .
	ps_blk_cards( $services, 3, true, 'ps-grid--services' ) .
	ps_blk_p( 'Technical and construction works shall be carried out according to the agreed contractual scope and in coordination with the relevant specialized contractors, suppliers and technical service providers.', 'ps-scope-note' ),
	'',
	'services'
);

// Section 2: Our Solution Areas (#sectors).
$services_page .= ps_blk_section(
	ps_blk_eyebrow( 'Solution Areas' ) .
	ps_blk_heading( 'Our Solution Areas', 2 ) .
	ps_blk_cards( $solutions, 2, false, 'ps-grid--solutions' ),
	'tint',
	'sectors'
);

// Section 3a: Investment Opportunity video (shown only once a video URL is set in the Customizer).
$services_page .= ps_blk_section(
	ps_blk_video_split(
		ps_blk_eyebrow( 'Investment Opportunity' ) .
		ps_blk_heading( 'The Opportunity Starts in Dammam', 2 ) .
		ps_blk_p( 'Modern refrigerated and frozen warehouse projects begin in the Eastern Province, the Kingdom\'s gateway to the Gulf, to serve market needs across the Kingdom as commercial and tourism activity grows and demand for cold supply chains increases.' ) .
		ps_blk_p( 'Watch the video to discover the opportunity and how PENTA SERVE helps you turn your investment idea into a fully developed project, backed by team members with more than 40 years of specialized professional experience.' ) .
		ps_blk_buttons( array( array( 'label' => 'Request a Discussion', 'url' => '#request', 'style' => 'primary' ) ) ),
		'[ps_video slot="invest"]'
	),
	'video',
	'opportunity'
);

// Section 3: Investment Opportunities (#investment) — prominent dark/blue + orange treatment, no video.
$services_page .= ps_blk_section(
	ps_blk_group(
		ps_blk_group(
			ps_blk_eyebrow( 'Investment Opportunities' ) .
			ps_blk_heading( 'Turn Your Investment Idea into a Fully Developed Project', 2 )
		, 'ps-invest-head' ) .
		ps_blk_group(
			ps_blk_p( 'We help investors interested in entering the refrigerated and frozen storage sector assess and develop their projects according to market requirements. The investor provides the capital and owns the project and its goods, while PENTA SERVE provides project development, design, equipment coordination, implementation coordination, operations and management services according to the agreed scope of work.' ) .
			ps_blk_p( 'We begin by assessing each investor\'s idea and project requirements to identify suitable solutions, define the scope of services and establish the cooperation framework before implementation begins.' )
		, 'ps-invest-copy' ) .
		ps_blk_shortcode( '[ps_image_slot slot="investment" variant="invest"]' ),
		'ps-invest-split'
	) .
	ps_blk_group(
		ps_blk_group(
			ps_blk_heading( 'The Investor\'s Role', 3 ) .
			ps_blk_list( array(
				'Providing the required investment capital.',
				'Owning the project and the goods stored.',
				'Making investment decisions in accordance with the agreed arrangement.',
			) ),
			'ps-role-card'
		) .
		ps_blk_group(
			ps_blk_heading( 'PENTA SERVE\'s Role', 3 ) .
			ps_blk_list( array(
				'Project assessment and development.',
				'Design and equipment coordination.',
				'Coordination of technical services, procurement, and preparation and implementation works through specialized entities, according to the agreed scope of work.',
				'Providing operational, management and supervision services according to the contractual agreement.',
			) ),
			'ps-role-card'
		),
		'ps-grid ps-grid--2'
	) .
	ps_blk_p( 'The responsibilities of each party, cooperation structure and scope of services shall be clearly defined through a formal contractual agreement.', 'ps-highlight-box' ),
	'invest',
	'investment'
);

// Section 4: Request Discussion (#request).
$services_page .= ps_blk_section(
	ps_blk_eyebrow( 'Request Discussion' ) .
	ps_blk_heading( 'Investment Project Discussion Form', 2 ) .
	ps_blk_p( 'Share your project details below so our team can assess your requirements and discuss the suitable cooperation framework with you.' ) .
	ps_blk_shortcode( '[ps_investment_form]' ) .
	ps_blk_p( 'This information is for general introduction and does not constitute a binding offer or a guarantee of any outcome. All services and responsibilities are governed solely by a formal written contractual agreement.', 'ps-note' ),
	'soft',
	'request'
);

/* --------------------------------------------------------------- CONTACT */
$contact = ps_blk_section(
	ps_blk_eyebrow( 'Contact Us' ) .
	ps_blk_heading( 'Contact Us', 2 ) .
	ps_blk_p( 'Let us help you assess your idea and develop your project in the refrigeration and freezing warehouse sector.' ) .
	ps_blk_group(
		ps_blk_shortcode( '[ps_contact_info]' ) . ps_blk_shortcode( '[ps_contact_form]' ),
		'ps-contact-grid'
	)
);

/* --------------------------------------------------------------- PRIVACY */
$privacy = ps_blk_section(
	ps_blk_p( 'Last updated: {date}', 'ps-note' ) .
	ps_blk_p( 'PENTA SERVE KSA respects your privacy. This policy explains what personal data we collect through this website, why we collect it, how we protect it and what rights you have over it. By using the website or submitting an inquiry form, you acknowledge that you have read this policy.' ) .

	ps_blk_heading( '1. Who We Are', 2 ) .
	ps_blk_p( 'This website is operated by PENTA SERVE KSA, a specialized partner in developing commercial refrigeration and freezing warehouse projects. For any privacy question you can reach us at {mail}.' ) .

	ps_blk_heading( '2. Data We Collect', 2 ) .
	ps_blk_p( 'When you submit a contact or project discussion form, we collect the information you provide: your full name, email address, phone number, subject or project details (proposed location, project type, expected storage capacity, project stage and additional details), and message. Please do not include sensitive personal information in your submission.' ) .
	ps_blk_p( 'Our web server may also automatically record technical information such as your IP address, browser type, device type and pages visited for security, troubleshooting and reliable website operation.' ) .

	ps_blk_heading( '3. How We Use Your Data', 2 ) .
	ps_blk_list( array(
		'To respond to your inquiry and communicate with you about your project.',
		'To assess project requirements and coordinate initial discussions.',
		'To protect the website against spam, abuse and security threats.',
		'To meet legal and regulatory obligations when they apply.',
	) ) .
	ps_blk_p( 'Submitted inquiries are sent to {mail}, and a private copy is stored in the website dashboard for follow-up.' ) .

	ps_blk_heading( '4. Cookies and Third-Party Services', 2 ) .
	ps_blk_p( 'The website may use technical cookies necessary for language preferences and core operation. Fonts are loaded from Google Fonts, and the introduction video on the Home page is embedded via YouTube (using youtube-nocookie.com). WhatsApp and phone links open the corresponding applications on your device and are governed by those providers\' terms.' ) .

	ps_blk_heading( '5. Sharing With Third Parties', 2 ) .
	ps_blk_p( 'We do not sell, rent or trade your personal data. We share data only when required by law or competent authorities, or as strictly needed to operate email and hosting infrastructure under confidentiality obligations.' ) .

	ps_blk_heading( '6. Protection and Retention', 2 ) .
	ps_blk_p( 'We apply reasonable technical and organizational safeguards against unauthorized access, loss or misuse, and retain inquiry records only for as long as needed for follow-up and applicable record-keeping obligations.' ) .

	ps_blk_heading( '7. Your Rights', 2 ) .
	ps_blk_p( 'In accordance with the Saudi Personal Data Protection Law, you may request access to, correction of, or deletion of your personal data by emailing {mail}.' ) .

	ps_blk_heading( '8. Contact', 2 ) .
	ps_blk_p( 'For any privacy-related request, please contact us at {mail}.' ),
	'narrow'
);

/* ----------------------------------------------------------------- TERMS */
$terms = ps_blk_section(
	ps_blk_p( 'Last updated: {date}', 'ps-note' ) .
	ps_blk_p( 'Welcome to the PENTA SERVE KSA website (pentaserveksa.com). By accessing or using this website, you agree to the terms below.' ) .

	ps_blk_heading( '1. About the Website', 2 ) .
	ps_blk_p( 'This website introduces PENTA SERVE KSA and its integrated services in developing commercial refrigeration and freezing warehouse projects, from initial project assessment, planning and design, through equipment coordination and implementation, to operations, management and supervision.' ) .

	ps_blk_heading( '2. Informational Nature & No Guarantee of Outcomes', 2 ) .
	ps_blk_p( 'The information provided on this website is for general introduction only and does not constitute a binding offer, financial or legal advice, or a guarantee of any outcome. Any cooperation framework, scope of services and contractual responsibility shall be governed solely by a formal written contractual agreement signed by both parties.' ) .

	ps_blk_heading( '3. Services and Coordination Scope', 2 ) .
	ps_blk_p( 'Technical and construction works are carried out according to the agreed contractual scope and in coordination with relevant specialized contractors, suppliers and technical service providers.' ) .

	ps_blk_heading( '4. Illustrative Images', 2 ) .
	ps_blk_p( 'Images displayed on this website are illustrative and represent conceptual storage facilities, refrigeration equipment and handling operations. They are not presented as completed projects of PENTA SERVE KSA unless explicitly stated otherwise.' ) .

	ps_blk_heading( '5. Intellectual Property', 2 ) .
	ps_blk_p( 'All website content, branding, logos, text and design belong to PENTA SERVE KSA or are used with permission and may not be reproduced or distributed without prior written consent.' ) .

	ps_blk_heading( '6. Acceptable Use', 2 ) .
	ps_blk_p( 'You agree to provide accurate information when submitting forms and not to misuse, disrupt or attempt unauthorized access to the website.' ) .

	ps_blk_heading( '7. Limitation of Liability & Applicable Law', 2 ) .
	ps_blk_p( 'To the extent permitted by law, PENTA SERVE KSA is not liable for any loss or damage arising from reliance on website content without a formal written agreement. These terms are governed by the laws and regulations of the Kingdom of Saudi Arabia.' ) .

	ps_blk_heading( '8. Contact', 2 ) .
	ps_blk_p( 'For questions regarding these terms, please contact {mail}.' ),
	'narrow'
);

/* ------------------------------------------------------------------ MAP */
return array(
	'home'     => array(
		'title'   => 'Home',
		'excerpt' => 'Invest in Saudi Arabia with PENTA SERVE KSA. We help investors develop integrated commercial refrigeration and freezing warehouse projects from concept to operation.',
		'content' => $home,
	),
	'about'    => array(
		'title'   => 'About Us',
		'excerpt' => 'PENTA SERVE KSA specializes in developing refrigeration and freezing warehouse solutions and projects across Saudi Arabia, from initial assessment to operations and management.',
		'content' => $about,
	),
	'services' => array(
		'title'   => 'Services & Investment Opportunities',
		'excerpt' => 'Explore PENTA SERVE KSA integrated cold storage services, solution areas, and investment project development framework in Saudi Arabia.',
		'content' => $services_page,
	),
	'contact'  => array(
		'title'   => 'Contact Us',
		'excerpt' => 'Contact PENTA SERVE KSA at info@pentaserveksa.com or +966 50 600 5453 to discuss your commercial refrigeration and freezing warehouse project.',
		'content' => $contact,
	),
	'privacy'  => array(
		'title'   => 'Privacy Policy',
		'excerpt' => 'Privacy Policy of PENTA SERVE KSA explaining how inquiry data is collected, used and protected.',
		'content' => $privacy,
	),
	'terms'    => array(
		'title'   => 'Terms & Conditions',
		'excerpt' => 'Terms and Conditions governing the use of the PENTA SERVE KSA website.',
		'content' => $terms,
	),
);