<?php 

namespace App;

use Kenjiefx\ScratchPHP\App\Events\BlockJSCollectedEvent;
use Kenjiefx\ScratchPHP\App\Events\ComponentJSCollectedEvent;
use Kenjiefx\ScratchPHP\App\Events\HTMLBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\JSBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\ListensTo;
use Kenjiefx\ScratchPHP\App\Events\PageBuildCompletedEvent;
use Kenjiefx\ScratchPHP\App\Events\SettingsRegisteredEvent;
use Kenjiefx\ScratchPHP\App\Extensions\ExtensionsInterface;

class Extension implements ExtensionsInterface {

    private bool $sayHelloWorld;

    public function __construct(

    ) {}

    #[ListensTo(SettingsRegisteredEvent::class)]
    public function configuration(array $configuration) {
        $this->sayHelloWorld = $configuration['sayHelloWorld'] ?? false;
    }

    #[ListensTo(ComponentJSCollectedEvent::class)]
    public function componentJS(ComponentJSCollectedEvent $event) {
        $content = $event->getContent();
        $wrapped = $this->wrapIFFE($content);
        $event->updateContent($wrapped);
    }

    #[ListensTo(BlockJSCollectedEvent::class)]
    public function blockJS(BlockJSCollectedEvent $event) {
        $content = $event->getContent();
        $wrapped = $this->wrapIFFE($content);
        $event->updateContent($wrapped);
    }

    /**
     * Wraps the JavaScript content in an IIFE (Immediately Invoked Function Expression).
     * This is a utility method that can be used directly if needed.
     */
    public function wrapIFFE($content) {
        if (trim($content) === '') return ''; 
        return <<<JS
            (function() {
                $content
            })();
        JS;
    }

    #[ListensTo(HTMLBuildCompletedEvent::class)]
    public function pageHTML(HTMLBuildCompletedEvent $event){
        $htmlContent = $event->getContent();
        $htmlContent = $this->sayHelloOrScratch($htmlContent);
        $event->updateContent($htmlContent);
    }

    private function sayHelloOrScratch(string $pageHtml) {
        if ($this->sayHelloWorld) {
            $pageHtml = str_replace('Hello, Scratch!', 'Hello, World!', $pageHtml);
            $pageHtml = str_replace('<code>Hello, World!</code>', '<code>Hello, Scratch!</code>', $pageHtml);
            return $pageHtml;
        } 
        return $pageHtml;
    }

    /**
     * Wraps the JavaScript content in JQuery document ready function.
     */
    #[ListensTo(JSBuildCompletedEvent::class)]
    public function wrapDocumentReady(JSBuildCompletedEvent $event){
        $content = $event->getContent();
        $wrapped = "$(document).ready(function() {\n PLACEHOLDER \n});";
        $wrapped = str_replace("PLACEHOLDER", $content, $wrapped);
        $event->updateContent($wrapped);
    }

}