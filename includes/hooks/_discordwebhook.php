<?php
if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

// Base url for your WHMCS admin portal
// If there is no url, this will NOT work
$GLOBALS['hook_baseurl'] = "https://whmcs.com/portal/admin"; // don't add the / at the end

// Username of the webhook (e.g. https://i.avasdemon.rocks/DiscordPTB_2018-06-21_13-14-43.png)
$GLOBALS['hook_username'] = "Crident Support Notification";

// 0xhex colors for the left borders - default are boobstrap colors
// ( https://getbootstrap.com/docs/4.0/utilities/colors/#background-color )
$GLOBALS['hook_colors'] = [
    "success" => 0x28a745,
    "danger" => 0xdc3545,
    "warning" => 0xffc107,
    "priamry" => 0x007bff,
    "info" => 0x17a2b8,
];

// These are all the hooks that you can enable/disable
$show_openedtickets = true; // When the ticket is created
$show_closedtickets = true; // When the ticket closes
$show_userreplies = true; // When the user replies to a ticket
$show_notereply = true; // When someone makes a note on a ticket
$show_ticketstatuschange = true; // When the ticket status gets changed
$show_ticketprioritychange = true; // When the ticket priority gets changed

/**
 * Trims strings that go over 100
 *
 * @param $value
 * @param int $chars
 * @return string
 */
function trim_string($str)
{
    if(mb_strwidth($str, 'UTF-8') <= 100){
        return $str;
    }
    return rtrim(mb_strimwidth($str, 0, 100, '', 'UTF-8'))."<...>";
}

/**
 * Post the request to discord's webhook endpoint (aka whichever url u set)
 *
 * @param $hook_content
 * @return mixed
 */
function createRequest($hook_content){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'multipart/form-data',
        'application/x-www-form-urlencoded'
    ]);
    curl_setopt($ch, CURLOPT_URL, ""); // Introduce your webhook endpoint HERE
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hook_content));

    $output = curl_exec($ch);

    echo curl_getinfo($ch, CURLINFO_HTTP_CODE);
    print_r($output);

    curl_close($ch);
}

/**
 * Triggers on a ticket being opened
 *
 * @hook TicketOpen
 * @var $hook_content
 * @return createRequest
 */
if($show_openedtickets === true):
    add_hook('TicketOpen', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['test'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'type' => 'rich', // discord is known for screwing their API, so let's add a fallback incase
                    'description' => trim_string($vars['message']),
                    'fields' => [
                        [
                            'name' => 'Department',
                            'value' => $vars['deptname'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'Priority',
                            'value' => $vars['priorty'],
                            'inline' => true,
                        ],
                    ],
                    'color' =>  $GLOBALS['hook_colors']['success'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers when the user replies to the ticket
 *
 * @hook TicketUserReply
 * @var $hook_content
 * @return createRequest
 */
if($show_userreplies === true):
    add_hook('TicketUserReply', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'type' => 'rich', // discord is known for screwing their API, so let's add a fallback incase
                    'description' => trim_string($vars['message']),
                    'fields' => [
                        [
                            'name' => 'Department',
                            'value' => $vars['deptname'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'Priority',
                            'value' => $vars['priorty'],
                            'inline' => true,
                        ],
                        [
                            'name' => 'Status',
                            'value' => $vars['status'],
                            'inline' => true,
                        ],
                    ],
                    'color' =>  $GLOBALS['hook_colors']['primary'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers on a ticket being closed
 *
 * @hook TicketClose
 * @var $hook_content
 * @return createRequest
 */
if($show_closedtickets === true):
    add_hook('TicketClose', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'description' => 'Ticket was closed...', // whmcs doesn't give any other info than ticketid srry
                    'type' => 'rich',
                    'color' =>  $GLOBALS['hook_colors']['danger'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers on a note being created
 *
 * @hook TicketAddNote
 * @var $hook_content
 * @return createRequest
 */
if($show_notereply === true):
    add_hook('TicketAddNote', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'description' => trim_string($vars['message']),
                    // No point in adding anything else since WHMCS only provides the admins ID (wtf even)
                    // Could do a simple query but this is purely hook based
                    'type' => 'rich',
                    'color' =>  $GLOBALS['hook_colors']['primary'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers on a status change
 *
 * @hook TicketStatusChange
 * @var $hook_content
 * @return createRequest
 */
if($show_ticketstatuschange === true):
    add_hook('TicketStatusChange', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'fields' => [
                        [
                            'name' => "Status changed to...",
                            'value' => $vars['status'],
                            'inline' => true,
                        ],
                    ],
                    'type' => 'rich',
                    'color' =>  $GLOBALS['hook_colors']['info'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers on a priority change
 *
 * @hook TicketPriorityChange
 * @var $hook_content
 * @return createRequest
 */
if($show_ticketprioritychange === true):
    add_hook('TicketPriorityChange', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'fields' => [
                        [
                            'name' => "Status changed to...",
                            'value' => $vars['status'],
                            'inline' => true,
                        ],
                    ],
                    'type' => 'rich',
                    'color' =>  $GLOBALS['hook_colors']['info'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;

/**
 * Triggers when the ticket is flagged
 *
 * @hook TicketFlagged
 * @var $hook_content
 * @return createRequest
 */
if($show_ticketflagged === true):
    add_hook('TicketFlagged', 1, function($vars) {
        $hook_content = [
            'username' => $GLOBALS['hook_username'],
            'embeds' => [
                [
                    'url' => $GLOBALS['hook_baseurl']."/supporttickets.php?action=view&id=".$vars['ticketid'],
                    'title' => "Ticket #".vars['ticketid'],
                    'fields' => [
                        [
                            'name' => "Flagged by...",
                            'value' => $vars['adminname'], // this is either the admin that GETS flagged or something else.. idk
                            'inline' => true,
                        ],
                    ],
                    'type' => 'rich',
                    'color' =>  $GLOBALS['hook_colors']['info'],
                    'timestamp' => date(DateTime::ISO8601),
                    'footer' => [
                        'text' => 'By Lunaversity',
                        'icon_url' => 'https://avasdemon.rocks/lunaversity.gif'
                    ]
                ],
            ]
        ];
        createRequest($hook_content);
    });
endif;
