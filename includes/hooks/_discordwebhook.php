<?php
include_once("_discordwebhook_config.php");

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
    $ch_options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 40,
        CURLOPT_HTTPHEADER => "Content-Type: application/json",
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($hook_content),
        CURLOPT_USERAGENT => "WHMCS Discord Webhook Request",
    ];
    $ch = curl_init($discord_webhook);
    curl_setopt_array($ch, $ch_options);

    curl_close($ch);

    return curl_exec($ch);
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
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
                'color' =>  $hook_colors['success'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
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
                'color' =>  $hook_colors['primary'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
                'title' => "Ticket #".vars['ticketid'],
                'description' => 'Ticket was closed...', // whmcs doesn't give any other info than ticketid srry
                'type' => 'rich',
                'color' =>  $hook_colors['danger'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
                'title' => "Ticket #".vars['ticketid'],
                'description' => trim_string($vars['message']),
                // No point in adding anything else since WHMCS only provides the admins ID (wtf even)
                // Could do a simple query but this is purely hook based
                'type' => 'rich',
                'color' =>  $hook_colors['primary'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
                'title' => "Ticket #".vars['ticketid'],
                'fields' => [
                    [
                        'name' => "Status changed to...",
                        'value' => $vars['status'],
                        'inline' => true,
                    ],
                ],
                'type' => 'rich',
                'color' =>  $hook_colors['info'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
                'title' => "Ticket #".vars['ticketid'],
                'fields' => [
                    [
                        'name' => "Status changed to...",
                        'value' => $vars['status'],
                        'inline' => true,
                    ],
                ],
                'type' => 'rich',
                'color' =>  $hook_colors['info'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
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
            'username' => $hook_username,
            'embeds' => [
                'url' => "$hook_baseurl/supporttickets.php?action=view&id=".$vars['ticketid'],
                'title' => "Ticket #".vars['ticketid'],
                'fields' => [
                    [
                        'name' => "Flagged by...",
                        'value' => $vars['adminname'], // this is either the admin that GETS flagged or something else.. idk
                        'inline' => true,
                    ],
                ],
                'type' => 'rich',
                'color' =>  $hook_colors['info'],
                'timestamp' => date('M - D'),
                'footer' => "Webhook by: Lunaversity"
            ],
        ];
        createRequest($hook_content);
    });
endif;