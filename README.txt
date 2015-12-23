Usage is best explained with an example, which we'll break down in parts as the syntax is fairly powerful
wp sweep --dry-run -t users,posts.post_author,posts.post_content,posts.post_title -f posts.post_author=random users.user_pass=auto users.user_email='ivan.k+__ID__@10up.com' posts.post_title=ipsum posts.post_content=markov -l users=10 posts=100.post_date

wp sweep
How you invoke the command

--dry-run
Output what we're going to do, without making any changes.

Tables
-t users,posts.post_author,posts.post_content,posts.post_title
Affected tables / table columns
Notice that we have ommitted wp_ from all of the tables? That's because we don't need $wpdb->prefix
Here we will sweep through the following tables:
users (all columns that have a replacement specified)
posts (post_author, post_content, post_title only if a replacement pattern is specified by default, or via override)

Format
-f posts.post_author=random users.user_pass=__user_email__UMINtHeroJEreAGleC users.user_email='ivank+__ID__@10up.com' posts.post_title=ipsum posts.post_content=markov
posts.post_author is set to a random user ID (from those that will remain after we've performed any adjustments to the users)
users.user_pass is set to the user email followed by UMINtHeroJEreAGleC
users.user_email='ivank+__ID__@10up.com' - __ID__ is replaced by the user ID
posts.post_title=ipsum replaces all post_titles with auto-generated lorem ipsum
posts.post_content=markov replaces all post_content with randomly generated content, using markov chains for the specified post_content

Limits
-l users=10 posts=page.100.post_date,post.50.post_content.length
users=10 only the first 10 users remain
posts=page.100.postdate,post.50.post_content.length we keep the following posts:
 post type = page, 100 posts sorted by postdate, descending
 post type = post, 50 posts with the longest post_content



Todo: custom formats via filters/actions
