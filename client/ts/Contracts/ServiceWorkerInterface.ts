import {SettingsInterface} from './SettingsInterface';

/**
 * Main service worker interface.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/ServiceWorker
 */
export interface ServiceWorkerInterface {
    /**
     * Settings.
     */
    readonly settings: SettingsInterface;
}
