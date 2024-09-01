Feature: Slider

  Scenario:
    Given I am on "form-control/slider.php"

    Then I check if input value for "//input[@name='slider_simple2_first']" match text "5.0"
    When I click using selector "//div[@id='atk_layout_maestro_form_form_layout_view']/following::div[contains(@class, 'thumb')]"
    //    When I fill in attribute "style" with "left: calc(20% - 10.5px); right: auto;" using selector "//div[@id='atk_layout_maestro_form_form_layout_view']/following::div[contains(@class, 'thumb')]"
    Then I check if input value for "//input[@name='slider_simple2_first']" match text "5.0"

