### Simple script that gives your discord webhook a friend

Just a simple simple script that sends notifcations to your discord webhook.
There are already a couple of these out there but this one doesn't send out information that shouldn't really be shown to your Support OPs (aka information about invoices n' stuff, regardless if it gives information it's still a useless and risky feature) although that depends on your team's structure.

## What does it do?

Mainly just sends out nice embedded notifications from tickets ranging from:
- Tickets being opened / closed
- User replies / Admin Replies
- Notes being added
- Ticket statuses being changed
- Ticket priority being changed
- Tickets being flagged

Pretty much anything ticket related on here: https://developers.whmcs.com/hooks-reference/ticket/
(I might add more stuff later considering WHMCS has localAPI() but that'll be later on if requested)

Thanks to Crident and @TrixterTheTux for testing this (I don't have WHMCS myself so that's y u see 2000 commits)
