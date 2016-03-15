#WP Hammer
``wp ha`` is a multi-tool. You can use it to clean your environment of personally identifiable information and extra content (posts and users) that are not necessary.

## WARNING ##
__WARNING__ All changes are final and modify your site DB. Make sure you take a backup of your database __BEFORE__ you play around with the tool ``wp db export``

## Before You Begin ##
Before you do anything, composer install / composer update, to fetch the dependencies.

## About ##

This tool will help you work on a client's site without having to worry about any of their user's personal information (emails, post content etc) being hosted on your dev environment.

## Installation ##
### 1. Fetch and build the package ###
```
cd ~/
git clone https://github.com/10up/wp-hammer.git 
cd wp-hammer
composer install
```

### 2. Choose install Method ###
You can install it as a plugin:
```
cd wp-content/plugins
mv ~/wp-hammer .
wp plugin activate wp-hammer
wp ha
```


You can call it from the command line
```
wp --require=~/wp-hammer/wp-hammer.php
```

You can add it to your [wp-cli config](http://wp-cli.org/config/).

You can add it as an alias in your `.bashrc`:
```
alias hammer='wp --require=$HOME/wp-hammer/wp-hammer.php'
```

With ``wp ha`` you can:

### Clean up user emails. ###
``wp ha -f users.user_email='ivan.k+__ID__@10up.com'``

### Clean up user passwords. ###
``wp ha -f users.user_pass=auto``

### Replace posts with dummy posts. ###
``wp ha -f posts.post_content=markov,posts.post_title=random``

### Remove extra users. ###
`` wp ha -l users=10``

### Remove extra Posts. ###
`` wp ha -l posts=100``


Or you can chain tasks together like in the following  example, which we'll break down in parts as the syntax is fairly powerful
wp ha -f posts.post_author=auto,users.user_pass=auto,users.user_email='ivan.k+__ID__@10up.com',posts.post_title=ipsum,posts.post_content=markov -l users=10,posts=100.post_date

``wp ha``
How you invoke the command

### Format
`
wp ha -f posts.post_author=auto users.user_pass=__user_email__UMINtHeroJEreAGleC users.user_email='ivank+__ID__@10up.com' posts.post_title=ipsum posts.post_content=markov
`
``posts.post_author`` is set to a random user ID (from those that will remain after we've performed any adjustments to the users)
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


### Another example ###
`
wp db import production.sql &&
wp ha posts.post_author=auto,users.user_pass=XGRwPjb7uFD5de23,users.user_email='ivan.k+__ID__@10up.com',posts.post_title=ipsum,posts.post_content=markov -l users=10 &&
wp db export staging.sql
`

### License ###
Copyright (c) 2015, Ivan Kruchkoff, 10up Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

### Credits ###


Created by Ivan Kruchkoff ( [@ivankk](https://profiles.wordpress.org/ivankk)), at [10up.com](http://10up.com).

Think this is a cool project? Love working with WordPress? [10up is hiring!](http://10up.com/careers/?utm_source=wphammer&utm_medium=community&utm_campaign=oss-code)