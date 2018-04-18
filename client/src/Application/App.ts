/**
 * Main application class.
 */
export class App {
    private _document: Document;

    /**
     * Constructor.
     */
    public constructor(document: Document) {
        this._document = document;
        this._document.addEventListener('DOMContentLoaded', () => this.addListeners());
    }

    /**
     * Add event listeners on document loaded.
     */
    private addListeners(): void {
        // handlers could be inline or...
        this.addListenerForClassName('sample-class-name', 'click', () => {});

        // ... be in their out methods.
        // this.addListenerForClassName('sample-class-name', 'click', (event: Event) => this.clickHandler(event));
    }

    // /**
    //  * Event handler sample.
    //  *
    //  * @param {Event} event
    //  */
    // private clickHandler(event: Event): void {
    //     console.log('click handler for type ' + event.type);
    // }

    /**
     * Add event listener by a class name.
     *
     * @param {string} className
     * @param {string} type
     * @param {EventListenerOrEventListenerObject} listener
     */
    private addListenerForClassName(className: string, type: string, listener: EventListenerOrEventListenerObject): void {
        const elements = this._document.getElementsByClassName(className);
        for (let i = 0; i < elements.length; ++i) {
            elements[i].addEventListener(type, listener);
        }
    }
}
