<?php

return [
    'name' => 'CareVision Healthcare Systems',
    'version' => '1.0.0',
    'description' => 'Advanced Medical Diagnosis Platform',
    'company' => 'Medical Solutions Inc.',
    'hospital' => "St. John's Medical College Hospital",
    'department' => 'Pathology-Central Laboratory',
    'year' => date('Y'),

    'id_card' => [
        'contact_phone' => env('HOSPITAL_PHONE', '+91 80 2206 5000'),
        'address' => env('HOSPITAL_ADDRESS', "Sarjapur Road, Bangalore, Karnataka 560034"),
        'website' => env('HOSPITAL_WEBSITE', 'www.stjohns.in'),
        'return_message' => 'If found, please return to the front desk',
    ],

    'blood_groups' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'],

    'payer_types' => [
        'self_pay' => 'Self Pay',
        'insurance' => 'Insurance',
    ],

    'payment_methods' => [
        'cash' => 'Cash',
        'card' => 'Card',
        'upi' => 'UPI',
        'cheque' => 'Cheque',
        'insurance' => 'Insurance',
    ],

    'phpmyadmin' => [
        'enabled' => filter_var(env('PHPMYADMIN_ENABLED', true), FILTER_VALIDATE_BOOLEAN),
        // Served from public/phpmyadmin (junction to C:\Laravel\phpmyadmin4.5). Run scripts/link-phpmyadmin.ps1 once.
        'url' => env('PHPMYADMIN_URL') ?: (rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/') . '/phpmyadmin'),
    ],

    'billing' => [
        'location_id' => (int) env('BILLING_LOCATION_ID', 10),
        'sequences' => [
            'bill_no' => 38,
            'op_receipt' => 4,
            'op_final' => 3,
        ],
        'receipt_prefix' => env('BILLING_RECEIPT_PREFIX', 'NHL-REC'),
        'final_prefix' => env('BILLING_FINAL_PREFIX', 'OPC'),
        'default_type_nr' => 925,
        'mtn_bank_label' => 'Mtn Main',
    ],

    'pid_prefix' => 'PID',
    'temp_prefix' => 'TEMP',
    'default_slot_duration' => 15,

    /*
    | Local fingerprint scanner bridge (Mantra, SecuGen, etc.).
    | Set FINGERPRINT_BRIDGE_URL in .env when a local capture service is running.
    */
    'fingerprint_bridge_url' => env('FINGERPRINT_BRIDGE_URL', 'http://127.0.0.1:8080/capture'),
    'fingerprint_bridge_timeout' => (int) env('FINGERPRINT_BRIDGE_TIMEOUT', 15),
    'fingerprint_simulation' => env('FINGERPRINT_SIMULATION', env('APP_DEBUG', false)),

    'departments' => [
        'Cardiology',
        'Neurology',
        'Orthopedics',
        'Pediatrics',
        'Dermatology',
        'Ophthalmology',
        'Dentistry',
        'General Medicine',
    ],

    'lab_departments' => [
        'biochemistry' => [
            'name' => 'Biochemistry',
            'prefix' => 'BIO',
        ],
        'hematology' => [
            'name' => 'Hematology',
            'prefix' => 'HEM',
        ],
        'microbiology' => [
            'name' => 'Microbiology',
            'prefix' => 'MIC',
        ],
    ],

    'lab_legacy_id_prefixes' => [
        'biochemistry' => '_bio',
        'hematology' => '_hem',
        'microbiology' => '_mic',
    ],

    'lab_billing_dept_nr' => [
        'biochemistry' => 22,
        'hematology' => 22,
        'microbiology' => 22,
    ],

    'sample_types' => [
        'Whole Blood',
        'Blood',
        'Serum',
        'Plasma',
        'Urine',
        'Stool',
        'Sputum',
        'Swab',
        'CSF',
        'Other',
    ],

    'sample_type_codes' => [
        'Whole Blood' => 'WB',
        'Blood' => 'BLD',
        'Serum' => 'SRM',
        'Plasma' => 'PLS',
        'Urine' => 'URN',
        'Stool' => 'STL',
        'Sputum' => 'SPT',
        'Swab' => 'SWB',
        'CSF' => 'CSF',
        'Other' => 'OTH',
    ],

    'lab_workflow_stages' => [
        'sample_acknowledgement' => [
            'label' => 'Sample Acknowledgement',
            'route' => 'laboratory.acknowledgement.index',
            'icon' => 'fas fa-clipboard-check',
        ],
        'sample_receive' => [
            'label' => 'Sample Receive',
            'route' => 'laboratory.receive.index',
            'icon' => 'fas fa-vial',
        ],
        'report_entry' => [
            'label' => 'Report Entry',
            'route' => 'laboratory.report-entry.index',
            'icon' => 'fas fa-edit',
        ],
        'authorization' => [
            'label' => 'Authorization',
            'route' => 'laboratory.authorization.index',
            'icon' => 'fas fa-user-check',
        ],
        'report_data' => [
            'label' => 'Report Data',
            'route' => 'laboratory.report-data.index',
            'icon' => 'fas fa-file-medical-alt',
        ],
    ],

    'pharmacy_sections' => [
        'sales' => [
            'label' => 'Sales',
            'route' => 'pharmacy.index',
            'icon' => 'fas fa-cash-register',
        ],
        'items' => [
            'label' => 'Item Creation',
            'route' => 'pharmacy.items.index',
            'icon' => 'fas fa-pills',
        ],
        'po' => [
            'label' => 'PO',
            'route' => 'pharmacy.po.index',
            'icon' => 'fas fa-file-invoice',
        ],
        'grn' => [
            'label' => 'GRN',
            'route' => 'pharmacy.grn.index',
            'icon' => 'fas fa-truck-loading',
        ],
    ],
];