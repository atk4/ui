
Agile UI is a project offered to you under MIT license. The authors and contributors have tried their best to
give you this project in best possible quality. But being an open-source project, we rely on continued support
and contribution, and to make sure quality of the project remains high, you would need to read and agree to
this document before contributing.

## Why contribute?

Contributing to open-source project is a great learning opportunity and also a way to demonstrate your skills
to potential employer or meet other like-minded developers.

Many contributors already use Agile UI in some commercial projects. If Agile UI does not fully satisfy requirements,
opening ticket and working with rest of community collectively is often a more efficient way to get your
project requirements fullfilled, gain long-term support for your contribution from community and also get paid
for the time you spend on contribution from your employee or a client.

## Ways to contribute

Agile UI grows rapidly following our [Roadmap](https://github.com/atk4/ui#bundled-componens), but once in a while
some extra components are contributed and accepted.

The other option is to help us clean up code / docs / tests for existing components. 

Here are some ideas on how you can help:

 - Read [documentation](http://agile-ui.readthedocs.io) and 
   [contribute clean-up, grammar fixes or link different topics together](https://github.com/atk4/ui/tree/develop/docs)
 - Read [the source](https://github.com/atk4/ui/tree/develop/src) and suggest minor clean-ups, improvements
   or extra comments.
 - Look through [Tickets](https://github.com/atk4/ui/issues?q=is%3Aissue+is%3Aopen+label%3A%22help+wanted%22) with tags
   **help wanted**. Those are specifically designed for new contributors. If you feel like you want to help with a ticket
   but do not understand requirements, ask for clarifications in the comments.
 - Integrations with other frameworks are important. We want to make Agile UI available in Yii, Laravel, Wordpress 
   and other frameworks. Your knowledge of other frameworks would be very helpful.
 - If you know JavaScript, we have [several issues](https://github.com/atk4/ui/issues?q=is%3Aissue+is%3Aopen+label%3AJS)
   that require your knowledge.
 
## Step-by-step Guide

First, set up Agile UI locally:

 1. Clone Agile UI repository locally into Webroot.
 2. Import "atk4.sql" into database and verify that "demos/" function fully.
 3. Compile JS [as described](https://github.com/atk4/ui/tree/develop/js) making sure you have dependencies.
 4. Compile CSS [by using this line](https://github.com/atk4/ui/blob/develop/tools/release.sh#L77)
 5. Ensure that your local demo uses your local JS/CSS files, not ones from CDN (in inspector or view source).
 
Next, create a new page (or change existing) inside "demos", which tries to replicate a bug or will demonstrate your
new component. Add or update other files inside "src/", and make sure your new component works well.

Finally, create a screenshot of your new feature and proceed to
[submit a Pull Request](https://help.github.com/articles/creating-a-pull-request/). If you are still working
on your PR, use label "work in progress", otherwise add "in review".

Your PR needs to be approved by one of our contributor team leaders.

Typically you would need to write documentation also, but it's better if you contribute your code first, receive
some feedback and write it later.
