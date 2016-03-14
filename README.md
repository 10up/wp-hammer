#WP Hammer
``wp ha`` is created to help you sweep up your environment of personally identifiable information and extra content that is not necessary.

## WARNING ##
__WARNING__ All changes are final and modify your site DB. Make sure you take a backup of your database __BEFORE__ you play around with the tool ``wp db export``

## ABOUT ##

This tool will help you work on a client's site without having to worry about any of their user's personal information (emails, post content etc) being hosted on your dev environment.

With ``wp ha`` you can:

  *  Clean up user emails. ``wp ha -f users.user_email='ivan.k+__ID__@10up.com``
  *  Clean up user passwords. ``wp ha -f users.user_pass=auto``
  *  Replace posts with dummy posts. ``wp ha -f posts.post_content=markov``
  *  Remove extra users. `` wp ha -l users=10``

Before you do anything, composer install, to fetch the dependencies

Usage is best explained with an example, which we'll break down in parts as the syntax is fairly powerful
wp ha -f posts.post_author=auto,users.user_pass=auto,users.user_email='ivan.k+__ID__@10up.com',posts.post_title=ipsum,posts.post_content=markov -l users=10,posts=100.post_date

wp ha
How you invoke the command

### Format
`
wp ha -f posts.post_author=auto users.user_pass=__user_email__UMINtHeroJEreAGleC users.user_email='ivank+__ID__@10up.com' posts.post_title=ipsum posts.post_content=markov
`
`posts.post_author` is set to a random user ID (from those that will remain after we've performed any adjustments to the users)
`users.user_pass` is set to the user email followed by UMINtHeroJEreAGleC
`users.user_email='ivank+__ID__@10up.com' - __ID__` is replaced by the user ID
`posts.post_title=ipsum` replaces all post_titles with auto-generated lorem ipsum
`posts.post_content=markov` replaces all post_content with randomly generated content, using markov chains for the specified post_content


### Limits
`
-l users=10 posts=page.100.post_date,post.50.post_content.length`
users=10 only the first 10 users remain
`posts=page.100.postdate,post.50.post_content.length `
We keep the following posts:
 post type = page, 100 posts sorted by postdate, descending
 post type = post, 50 posts with the longest post_content
 `


### Another example
`
wp db import production.sql &&
wp ha posts.post_author=auto,users.user_pass=XGRwPjb7uFD5de23,users.user_email='ivan.k+__ID__@10up.com',posts.post_title=ipsum,posts.post_content=markov -l users=10 &&
wp db export sweep.sql
`

Created by Ivan Kruchkoff ( @ivankk on WordPress.org ), at 10up.com.
