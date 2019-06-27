# Contributing to `cloudonix-php`

We'd love for you to contribute to our source code and to make `cloudonix-php` 
more than it is! Here are the guidelines we'd like you to follow:

 - [Code of Conduct](#coc)
 - [Questions?](#question)
 - [Bugs! Bugs! Bugs!](#bugs)
 - [Feature Requests](#feature)
 - [Submission Guidelines](#submit)
 - [Coding Rules](#rules)

## <a name="coc"></a> Code of Conduct

Help us keep `cloudonix-php` open, free and inclusive. Please be kind to to your fellow developers, 
we all have the same goal: make `cloudonix-php` as good and as fun to use as possible.

## <a name="question"></a> Got an API/Product Question or Problem?

If you have questions about how to use `cloudonix-php`, simply open an issue in the project issue 
tracker, we'll do our best to get to it as fast as possible.

## <a name="bugs"></a> Bugs! Bugs! Bugs!

cloudonix-php is `artisan code` - which means, it may (and most probably) include some bugs. If you
 encouter these, simply open an issue in the tracker - we'll jump on it as fast as possible.

**Please see the [Submission Guidelines](#submit) below.**

## <a name="feature"></a> Want a Feature?

Please submit feature requests on the issue tracker. If you would like to implement a new feature 
please consider the following:

* **Major Changes** should be openly discussed with the contributors - via opening an issue or 
submitting a pull request.
* **Small Changes** can be submitted to the [GitHub Repository][github] as Pull Requests.

## <a name="submit"></a> Submission Guidelines

### Submitting an Issue
Before you submit your issue search the archive, maybe your question was already
answered.

If your issue appears to be a bug, and hasn't been reported, open a new issue.
Help us to maximize the effort we can spend fixing issues and adding new
features by not reporting duplicate issues. Providing the following information
will increase the chances of your issue being dealt with quickly:

* **Overview of the Issue** - if an error is being thrown a non-minified stack
  trace helps
* **Motivation for or Use Case** - explain why this is a bug for you
* **`twilio-php` Version(s)** - is it a regression?
* **Operating System (if relevant)** - is this a problem with all systems or
  only specific ones?
* **Reproduce the Error** - provide an isolated code snippet or an unambiguous
  set of steps.
* **Related Issues** - has a similar issue been reported before?
* **Suggest a Fix** - if you can't fix the bug yourself, perhaps you can point
  to what might be causing the problem (line of code or commit)

**If you get help, help others. Good karma rules!**

### Submitting a Pull Request
Before you submit your pull request consider the following guidelines:

* Search [GitHub][github] for an open or closed Pull Request that relates to
  your submission.
* Make your changes in a new git branch:

    ```shell
    git checkout -b my-fixer-upper-branch master
    ```

* Create your patch, **including appropriate test cases**.
* Follow our [Coding Guidelines](#guidelines).
* Run the full `cloudonix-php` test suite, and ensure that all tests pass.
* Commit your changes using a descriptive commit message.

    ```shell
    git commit -a
    ```
* Build your changes locally to ensure all the tests pass:

    ```shell
    make test
    ```

* Push your branch to GitHub:

    ```shell
    git push origin my-fixer-upper-branch
    ```

In GitHub, send a pull request to `cloudonix-php:master`.
We may suggest changes or modifications to your submission. In such a case:

* Make the required updates.
* Re-run the `cloudonix-php` test suite to ensure tests are still passing.
* Commit your changes (e.g. `my-fixer-upper-branch`).
* Push the changes (this will update your Pull Request).

That's it! Thank you for your contribution!

#### Post Pull Request Merge

After the pull request is merged, safely delete your branch and pull
the the latest changes from upstream.

## <a name="guidelines"></a> Coding Guidelines

To ensure consistency throughout the source code, keep these rules in mind as
you are working:

* All features or bug fixes **must be tested** by one or more tests.
* All classes and methods **must be documented**.
* All changes and modifications **must conform to the existing design pattern**.

[issue-link]: https://github.com/cloudonix/cloudonix-php/issues/new
[github]: https://github.com/cloudonix/cloudonix-php