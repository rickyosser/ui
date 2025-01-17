Feature: Table Filter

  Scenario:
    Given I am on "collection/tablefilter.php"
    Then I should see "Australia"
    Then I click filter column name "atk_fp_country__name"
    When I fill field using "//div.popup[2]//input[@name='value']" with "united kingdom"
    When I click using selector "//div.popup[2]//div[text()='Set']"
    Then I should not see "Australia"
    Then I should see "United Kingdom"
    Then I click filter column name "atk_fp_country__phonecode"
    When I fill field using "//div.popup[6]//input[@name='value']" with "44"
    When I click using selector "//div.popup[6]//div[text()='Set']"
    Then I should see "United Kingdom"
    Then I click filter column name "atk_fp_country__phonecode"
    When I fill field using "//div.popup[6]//input[@name='value']" with "4"
    When I click using selector "//div.popup[6]//div[text()='Set']"
    Then I should not see "United Kingdom"
    Then I should see "No records"
    Then I click filter column name "atk_fp_country__phonecode"
    When I click using selector "//div.popup[6]//div[text()='Clear']"
    Then I should not see "No records"
    Then I should see "United Kingdom"
    Then I click filter column name "is_uk"
    Then I select value "Is No" in lookup "//div.popup[7]//input[@name='op']"
    When I click using selector "//div.popup[7]//div[text()='Set']"
    Then I should see "No records"
    Then I click filter column name "is_uk"
    Then I select value "Is Yes" in lookup "//div.popup[7]//input[@name='op']"
    When I click using selector "//div.popup[7]//div[text()='Set']"
    Then I should see "United Kingdom"
    Then I press button "Clear Filters"
    Then I should not see "United Kingdom"
    Then I should see "Australia"
    Then I should see "Argentina"
    Then I should see "Austria"
    Then I click filter column name "atk_fp_country__id"
    When I select value "=" in lookup "//div.popup[1]//input[@name='op']"
    When I fill field using "//div.popup[1]//input[@name='value']" with "13"
    When I click using selector "//div.popup[1]//div[text()='Set']"
    Then I should see "Australia"
    Then I should not see "Argentina"
    Then I should not see "Austria"
    Then I click filter column name "atk_fp_country__id"
    When I select value "< or equal" in lookup "//div.popup[1]//input[@name='op']"
    When I click using selector "//div.popup[1]//div[text()='Set']"
    Then I should see "Australia"
    Then I should see "Argentina"
    Then I should not see "Austria"
