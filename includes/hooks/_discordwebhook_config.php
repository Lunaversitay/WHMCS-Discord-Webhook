<?php

// Discord webhook url (https://i.avasdemon.rocks/DiscordPTB_2018-06-21_13-58-47.png)
$discord_webhook = "";

// Base url for your WHMCS admin portal
$hook_baseurl = "https://whmcs.com/portal/admin"; // don't add the / at the end

// Username of the webhook (e.g. https://i.avasdemon.rocks/DiscordPTB_2018-06-21_13-14-43.png)
$hook_username = "Crident Support Notification";

// 0xhex colors for the left borders - default are boobstrap colors
// ( https://getbootstrap.com/docs/4.0/utilities/colors/#background-color )
$hook_colors = [
    "success" => '0x28a745',
    "danger" => '0xdc3545',
    "warning" => '0xffc107',
    "priamry" => '0x007bff',
    "info" => '0x17a2b8',
];

// These are all the hooks that you can enable/disable
$show_openedtickets = true; // When the ticket is created
$show_closedtickets = true; // When the ticket closes
$show_userreplies = true; // When the user replies to a ticket
$show_notereply = true; // When someone makes a note on a ticket
$show_ticketstatuschange = true; // When the ticket status gets changed
$show_ticketprioritychange = true; // When the ticket priority gets changed