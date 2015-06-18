<?php

namespace Swissbib\Record\Form;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("copy")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 */
class CopyForm
{
    /**
     * @Annotation\Name("pickup-locations")
     * @Annotation\Options({"label":"pick_up_location"})
     * @Annotation\Type("Zend\Form\Element\Select")
     */
    public $pickup_locations;

    /**
     * @Annotation\Name("sub-author")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Author"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[\w\s\d.\/(),-]*$/u"}})
     * @Annotation\ErrorMessage("input_too_long_or_disallowed")
     */
    public $sub_author;

    /**
     * @Annotation\Name("sub-title")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Title"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[\w\s\d.\/(),-]*$/u"}})
     * @Annotation\ErrorMessage("input_too_long_or_disallowed")
     */
    public $sub_title;

    /**
     * @Annotation\Name("pages")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Pages"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[\w\s\d.\/(),-]*$/u"}})
     * @Annotation\ErrorMessage("input_too_long_or_disallowed")
     */
    public $pages;

    /**
     * @Annotation\Name("note1")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"note1"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[\w\s\d.\/(),-]*$/u"}})
     * @Annotation\ErrorMessage("input_too_long_or_disallowed")
     */
    public $note1;

    /**
     * @Annotation\Name("note2")
     * @Annotation\Attributes({"type":"text"})
     * @Annotation\Options({"label":"Comment"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":0, "max":50}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"/^[\w\s\d.\/(),-]*$/u"}})
     * @Annotation\ErrorMessage("input_too_long_or_disallowed")
     */
    public $note2;
}