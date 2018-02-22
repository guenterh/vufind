import * as $ from "jquery";

import AutoSuggest from "./autosuggest/AutoSuggest";
import Configuration from "./autosuggest/Configuration";
import Settings from "./autosuggest/Settings";
import RecordRenderer from "./RecordRenderer";
import CarouselManager from "./carousel/CarouselManager";
import MediaQueryObserver from "./common/MediaQueryObserver";
import BackToTopButton from "./components/BackToTopButton";

$(document).ready(() => {
    const recordRenderer = new RecordRenderer(window.location.origin + VuFind.path + "/AJAX/Json");
    const recordIdEl: HTMLInputElement = $("input#record_id")[0] as HTMLInputElement;
    const contributorsList: HTMLElement = $(".sidebar .list-group.author")[0];
    const contributorsTemplate: any = (p: any) => {
        if (!p.name) {
            return "";
        }
        return `<li class="list-group-item"><a href="${VuFind.path}/Search/Results?lookfor=${p.name}&amp;type=Author" title="${p.name}">${p.name}</a><a href="${VuFind.path}/Card/Knowledge/Person/${p.id}" data-lightbox>
<span ${ p.hasSufficientData === "1" ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"
authorid="${p.id}"></span></a></li>`;
    };

    const subjects: JQuery<HTMLElement> = $(".subject [subjectid]");
    const subjectsTemplate: any = (s: any) => {
        return `<a href="${VuFind.path}/Card/Knowledge/Subject/${s.id}" data-lightbox>
<span ${ s.hasSufficientData === "1" ? ' class="fa fa-info-circle fa-lg"' : "" } style="display: inline;"</span></a>`;
    };
    if (recordIdEl) {
        recordRenderer.renderContributors(recordIdEl.value, contributorsTemplate, contributorsList)
            .then(() => {
                $(contributorsList).parent("div").toggleClass("hidden");
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
        recordRenderer.renderSubjects(subjects, subjectsTemplate)
            .then(() => {
                // TODO Required to bind lightbox. Is this the correct way?
                VuFind.lightbox.init();
            });
    }

    // setup auto-suggest
    const settings: Settings = swissbib.autoSuggestConfiguration();
    const autoSuggestConfiguration: Configuration = new Configuration(settings, VuFind);
    const autoSuggest = new AutoSuggest("#searchForm_lookfor", autoSuggestConfiguration);

    autoSuggest.initialize();

    // carousel
    const carouselManager: CarouselManager = new CarouselManager(swissbib.carousel, new MediaQueryObserver());
    carouselManager.initialize();
    carouselManager.activate();
    swissbib.carouselManager = carouselManager;

    // components
    const backToTopButtonDom: string = '<a id="back-to-top-btn" href="#" class="hidden-md hidden-lg"></a>';
    const backToTopButton: BackToTopButton = new BackToTopButton(backToTopButtonDom);
    backToTopButton.initialize();
});
