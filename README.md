# Selbstsein Community

Additional settings and features for the Selbstsein HumHub community.

## 0.1.0 — Space member visibility

Space header controls → **Selbstsein Community** provides three audiences:

* All authenticated users who can access the Space
* Members of this Space
* Space administrators and community administrators

Space owners and users with HumHub's ManageSpaces permission count as Space administrators, following HumHub's own authorization. Administrators always retain access. Guests never gain access. Existing Space accessibility and individual profile visibility still apply.

Installation alone changes no existing Space settings. Saving a policy explicitly enables it for that Space and sets Core `hideMembers=true`. A separate sidebar entry opens the permitted member list. This preserves suppression of Core member counters, directory member previews and newly generated join/leave activities. The Core advanced “Hide Members” switch must remain enabled for configured Spaces.

Direct Core member-list and membership-search requests enforce the audience, including AJAX. The administrative member management remains controlled by HumHub. Existing posts, historical activities, mentions and other content identifying members are not erased or concealed. REST API and third-party modules are not yet audited; do not treat this release as complete membership secrecy across all integrations.

## Installation and validation status

Module ID/folder: `selbstsein-community`; namespace: `humhub\\modules\\selbstseinCommunity`.

Developed against the HumHub 1.18.5 source. PHP 8.3 syntax checks and a standalone 80-case access-policy matrix pass. Full HumHub integration and browser acceptance testing are still required before production activation, especially the enabled REST API and custom modules. This is a development release, not a verified production rollout.

Copy into `protected/modules/selbstsein-community` and activate through the module administration on a test installation. Verify guests, signed-in nonmembers, members, moderators, Space admins and global admins against all three audiences, direct list/search URLs, private Spaces, sidebar and About page. Keep this release off production until those checks pass.

On disabling/removing the module, its event guards stop applying; Core hideMembers remains enabled and Core privileged-role access applies again. Existing policy values remain for reactivation. No membership records are changed.

Run `php tests/policy.php` for the isolated policy checks.
