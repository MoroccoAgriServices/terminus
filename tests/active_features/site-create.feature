Feature: Create a site
  In order to use the Pantheon platform
  As a user
  I need to be able to create a site on it.

  Background: I am authenticated
    Given I am authenticated

  @vcr sites_create
  Scenario: Create Site
    When I run "terminus site:create [[test_site_name]] [[test_site_name]] e8fe8550-1ab9-4964-8838-2b9abdccf4bf"
    Then I should get: "Creating a new site..."
    And I should get: "."
    And I should get: "Deploying CMS..."
    And I should get: "."
    And I should get: "Deployed CMS"
