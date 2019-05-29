# Spy Table of Contents (Under construction...)
**Spy Table of Contents** is a WordPress plugin that easily realizes a table of contents that spies the page or the post.

*Read this in other languages: [English](README.md), [日本語](README.ja.md)*

## Table of Contents
  - [Shortcodes](#Shortcodes)
    - [[toc]](#toc)
    - [[no_toc]](#no_toc)
  - [Credit](#Credit)




## Shortcodes

### [toc]
Lets you generate the table of contents at the preferred position.

ATTRIBUTES     | DESCRIPTION
---------------|-------------------------------------
label          | text, title of the table of contents
no_label       | true/false, shows or hides the title
wrapping       | `"left"` or `"right"`
heading_levels | numbers, this lets you select the heading levels you want included in the table of contents. Separate multiple levels with a comma. Example: include headings 3, 4 and 5 but exclude the others with `heading_levels="3,4,5"`
exclude        | text, enter headings to be excluded. Separate multiple headings with a pipe &#124;. Use an asterisk * as a wildcard to match other text. You could also use regular expressions for more advanced 
class          | text, enter CSS classes to be added to the container. Separate multiple classes with a space.

### [no_toc]
Allows you to disable the table of contents for the current post, page, or custom post type.

## Credit
**Spy Table of Contents** is a fork of the excellent [Table of Contents Plus](https://wordpress.org/plugins/table-of-contents-plus/) plugin by [Michael Tran](http://dublue.com/plugins/toc/).


