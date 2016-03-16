Checklists for Pull requests
----------------------------

About pull request itself:
- [ ] My changes contain only a single type of change (one bugfix or feature). 
- [ ] I have read and understood CONTRIBUTING guide

Code Coverage:
(not applicable for non-code fixes of course)
- [ ] My pull request has tests that cover 100% of the updated code. (phpunit --coverage-html code-coverage)
- [ ] My pull request does not remove any tests that are still needed.

Commits:
- [ ] My commits are logical, easily readable, with concise comments.
- [ ] My code follows PSR standards of coding style

Licensing:
- [ ] I am the author of submission or have been authorized by submission copyright holder to issue this pull request.
- [ ] Any new files contain the copyright header directly after the opening <?php line

Branching:
- [ ] My submission is based on dev branch
- [ ] My submission is compatible with latest master branch updates (no conflicts, I did a rebase if it was necessary).
- [ ] The name of the branch I want to merge upstream is not 'master'.
- [ ] My branch name follows the pattern *feature/some-new-shiny-feature* (for new features).
- [ ] My branch name follows the pattern *bugfix/some-bugfix* (for bugfixes).

Continuous integration:
- [ ] Once I will submit this pull request, I will wait for Travis-CI report (normally a couple of minutes) and fix any issues I might have introduced.



Pull request description
------------------------