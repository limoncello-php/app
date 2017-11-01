import {JsonApiError} from '../JsonApi/JsonApiError';
import {ApplicationInterface} from '../Contracts/ApplicationInterface';
// noinspection SpellCheckingInspection
import timeago from 'timeago.js';

const MSG_LOGIN_FAILED = 'Invalid email or password';

const CLASS_IS_ACTIVE = 'is-active';

const SELECTOR_HAMBURGER = '.hamburger';

const SELECTOR_POPUP = '.popup';
const SELECTOR_POPUP_LOGIN_SET = '.login-set';
const SELECTOR_POPUP_LOGIN_SET__EMAIL = 'input[type="email"]';
const SELECTOR_POPUP_LOGIN_SET__PASSWORD = 'input[type="password"]';
const SELECTOR_POPUP_LOGIN_SET__BUTTON = 'input[type="submit"]';
const SELECTOR_POPUP_LOGIN_SET__ERROR_MESSAGE = 'h2';
const SELECTOR_POPUP_LOGOUT_SET = '.logout-set';
const SELECTOR_POPUP_LOGOUT_SET__BUTTON = 'input[type="submit"]';

const SELECTOR_COMMENT_NEW = '.content-post .content-post_new-comment';
const SELECTOR_COMMENT_NEW__TEXT = 'textarea';
const SELECTOR_COMMENT_NEW__BUTTON = 'button';

const SELECTOR_POST_MAIN = '.content-post .content-post_main';
const POST_MAIN_ATTR_POST_ID = 'data-post-id';

const SELECTOR_BOARD_MAIN = '.content-board';

const SELECTOR_POST_NEW = '.content-board .content-board_new-post';
const SELECTOR_POST_NEW__TITLE = 'input[type="text"]';
const SELECTOR_POST_NEW__TEXT = 'textarea';
const SELECTOR_POST_NEW__BUTTON = 'button';
const BOARD_MAIN_ATTR_BOARD_ID = 'data-board-id';

const SELECTOR_BOARD_POSTS = '.content-board .content-board_posts';
const SELECTOR_BOARD_POST = '.content-board .content-board_posts .content-board_posts_post';
const SELECTOR_BOARD_POST__DELETE_BUTTON = 'button';
const BOARD_POST_ATTR_POST_ID = 'data-post-id';
const BOARD_POST_ATTR_AUTHOR_ID = 'data-author-id';
const BOARD_POST_CLASS = 'content-board_posts_post';

const SELECTOR_POST_COMMENTS = '.content-post .content-post_comments';
const SELECTOR_POST_COMMENT = '.content-post .content-post_comments .content-post_comments_comment';
const POST_COMMENT_CLASS = 'content-post_comments_comment';
const SELECTOR_POST_COMMENT__DELETE_BUTTON = 'button';
const POST_COMMENT_ATTR_COMMENT_ID = 'data-comment-id';
const POST_COMMENT_ATTR_AUTHOR_ID = 'data-author-id';

const SELECTOR_TIME = 'time';
const TIME_ATTR_SECONDS_TIMESTAMP = 'data-timestamp';

const SCOPE_CAN_ADMIN_BOARDS = 'manage_boards';

/**
 * Integrates DOM and application implementation (OAuth, API calls, caching, etc).
 */
export class Events {
    /**
     * @internal
     */
    private readonly application: ApplicationInterface;

    /**
     * @internal
     */
    private _hamburger: Element | undefined;

    /**
     * @internal
     */
    private _popup: Element | undefined;

    /**
     * @internal
     */
    private _loginSet: HTMLFieldSetElement | undefined;

    /**
     * @internal
     */
    private _errorMessage: HTMLHeadingElement | undefined;

    /**
     * @internal
     */
    private _emailInput: HTMLInputElement | undefined;

    /**
     * @internal
     */
    private _passwordInput: HTMLInputElement | undefined;

    /**
     * @internal
     */
    private _loginButton: HTMLInputElement | undefined;
    /**
     * @internal
     */
    private _logoutSet: HTMLFieldSetElement | undefined;

    /**
     * @internal
     */
    private _logoutButton: HTMLInputElement | undefined;

    /**
     * @param {ApplicationInterface} application
     */
    public constructor(application: ApplicationInterface) {
        this.application = application;
        this._hamburger = undefined;
        this._popup = undefined;
        this._loginSet = undefined;
        this._emailInput = undefined;
        this._passwordInput = undefined;
        this._loginButton = undefined;
        this._errorMessage = undefined;
        this._logoutSet = undefined;
        this._logoutButton = undefined;

        this.setLoginLogoutVisibilityInPopup();
        this.initData();
        this.initListeners();

        this.runTimestampHumanize();
        this.runUpdateUI();
    }

    /**
     * Handles click on login.
     *
     * @param {Event} event
     */
    private onLoginClick(event: Event): void {
        event.preventDefault();

        this.lockFormSubmit();

        this.clearErrorMessage();

        const email: string = this.emailInput.value;
        const password: string = this.passwordInput.value;

        this.application.requestAuthToken(email, password)
            .then(() => {
                this.unlockFormSubmit();
                this.setPopupVisible(false);
                this.clearPassword();
                this.setLoginLogoutVisibilityInPopup();
            })
            .catch((error) => {
                this.setErrorMessage(MSG_LOGIN_FAILED);
                this.clearPassword().focus();
                this.unlockFormSubmit();
                console.error('Login failed. ' + JSON.stringify(error));
            });
    }

    /**
     * Handles click on 'logout'.
     *
     * @param {Event} event
     */
    private onLogoutClick(event: Event): void {
        event.preventDefault();

        this.application.forgetAuthToken();
        this.setPopupVisible(false);
        this.setLoginLogoutVisibilityInPopup();
    }

    /**
     * Init data in UI.
     */
    private initData(): void {
        const userName = this.application.lastUserName;
        if (userName) {
            this.emailInput.value = userName;
        }
    }

    /**
     * Init event listeners.
     */
    private initListeners(): void {
        this.hamburger.addEventListener('click', () => this.togglePopup());
        this.loginButton.addEventListener('click', (event) => this.onLoginClick(event));
        this.logoutButton.addEventListener('click', (event) => this.onLogoutClick(event));

        // hide popup if anything other than the popup clicked
        window.document.addEventListener('mousedown', (event) => {
            const target = <HTMLElement>event.target;
            if (this.popup.contains(target) === false && this.hamburger.contains(target) === false) {
                this.setPopupVisible(false);
            }
        });

        // remove post
        const posts = Events.getElements(SELECTOR_BOARD_POST);
        for (let i = 0; i < posts.length; ++i) {
            const post = <HTMLButtonElement>posts[i];
            const deleteButton: HTMLButtonElement = post.querySelector(SELECTOR_BOARD_POST__DELETE_BUTTON);
            deleteButton.addEventListener('click', () => deletePost(post));
        }

        const deletePost = (post: HTMLElement): void => {
            const postId = parseInt(post.getAttribute(BOARD_POST_ATTR_POST_ID));
            this.application.apiDelete(`/posts/${postId}`)
                .then(() => post.remove())
                .catch((error) => {
                    console.error(`Delete post ${postId} failed.`);
                    console.debug(error);
                });
        };

        // remove comment
        const deleteComment = (comment: HTMLElement): void => {
            const commentId = parseInt(comment.getAttribute(POST_COMMENT_ATTR_COMMENT_ID));
            this.application.apiDelete(`/comments/${commentId}`)
                .then(() => comment.remove())
                .catch((error: JsonApiError) => {
                    console.error(`Delete comment ${commentId} failed.`);
                    console.debug(error);
                });
        };

        const comments = Events.getElements(SELECTOR_POST_COMMENT);
        for (let i = 0; i < comments.length; ++i) {
            const comment = <HTMLElement>comments[i];
            const deleteButton: HTMLButtonElement = comment.querySelector(SELECTOR_POST_COMMENT__DELETE_BUTTON);
            deleteButton.addEventListener('click', () => deleteComment(comment));
        }

        // create HTML elements helper
        const createElement = (tagName: string, text: string | null = null, attributes: any = {}, classes: string[] = []): HTMLElement => {
            let element = document.createElement(tagName);

            if (text !== null) {
                element.textContent = text;
            }
            for (let name in attributes) {
                if (attributes.hasOwnProperty(name)) {
                    element.setAttribute(name, attributes[name]);
                }
            }
            for (let name of classes) {
                element.classList.add(name);
            }

            return element;
        };

        // add new post handler
        const newPostForm = Events.getElement(SELECTOR_POST_NEW);
        if (newPostForm !== null) {
            const newPostSubmit = newPostForm.querySelector(SELECTOR_POST_NEW__BUTTON);
            newPostSubmit.addEventListener('click', () => {
                const postTitleElement = <HTMLInputElement>newPostForm.querySelector(SELECTOR_POST_NEW__TITLE);
                const postTextElement = <HTMLTextAreaElement>newPostForm.querySelector(SELECTOR_POST_NEW__TEXT);
                const boardId = parseInt(Events.getElement(SELECTOR_BOARD_MAIN).getAttribute(BOARD_MAIN_ATTR_BOARD_ID));
                const data = {
                    data: {
                        type: 'posts',
                        attributes: {
                            title: postTitleElement.value,
                            text: postTextElement.value
                        },
                        relationships: {
                            board: {
                                data: {type: 'boards', id: boardId}
                            }
                        }
                    }
                };
                const userName = this.application.userName;
                this.application.apiCreate('/posts', JSON.stringify(data))
                    .then(json => {
                        const postId = json.data.id;
                        let postElement = createElement('article', null, {
                            [BOARD_POST_ATTR_POST_ID]: postId,
                            [BOARD_POST_ATTR_AUTHOR_ID]: json.data.relationships.user.data.id
                        }, [BOARD_POST_CLASS]);

                        const postLink = createElement('a', null, {href: `/posts/${postId}`});
                        postLink.insertBefore(createElement('h1', json.data.attributes.title), null);
                        postElement.insertBefore(postLink, null);

                        let postText: string = json.data.attributes.text;
                        postText = postText.length > 120 ? postText.substr(0, 117) + '...' : postText;
                        postElement.insertBefore(createElement('h2', postText), null);

                        postElement.insertBefore(createElement('h3', userName), null);

                        postElement.insertBefore(createElement('time', null, {
                            TIME_ATTR_SECONDS_TIMESTAMP: String(Date.parse(json.data.attributes['created-at']) / 1000)
                        }), null);
                        const button = createElement('button', 'Delete');
                        button.addEventListener('click', () => deletePost(postElement));
                        postElement.insertBefore(button, null);

                        postTitleElement.value = null;
                        postTextElement.value = null;
                        Events.getElement(SELECTOR_BOARD_POSTS).insertBefore(postElement, null);
                    })
                    .catch((error: JsonApiError) => {
                        console.error('Adding post failed.');
                        console.debug(JSON.stringify(error));
                    });
            });
        }

        // add new comment handler
        const newCommentForm = Events.getElement(SELECTOR_COMMENT_NEW);
        if (newCommentForm !== null) {
            const newCommentSubmit = newCommentForm.querySelector(SELECTOR_COMMENT_NEW__BUTTON);
            newCommentSubmit.addEventListener('click', () => {
                const commentTextElement = newCommentForm.querySelector(SELECTOR_COMMENT_NEW__TEXT);
                const postId = parseInt(Events.getElement(SELECTOR_POST_MAIN).getAttribute(POST_MAIN_ATTR_POST_ID));
                const data = {
                    data: {
                        type: 'comments',
                        attributes: {
                            text: commentTextElement.value
                        },
                        relationships: {
                            post: {
                                data: {type: 'posts', id: postId}
                            }
                        }
                    }
                };
                const userName = this.application.userName;
                this.application.apiCreate('/comments', JSON.stringify(data))
                    .then(json => {
                        let commentElement = createElement('article', null, {
                            [POST_COMMENT_ATTR_COMMENT_ID]: json.data.id,
                            [POST_COMMENT_ATTR_AUTHOR_ID]: json.data.relationships.user.data.id
                        }, [POST_COMMENT_CLASS]);

                        commentElement.insertBefore(createElement('h1', json.data.attributes.text), null);
                        commentElement.insertBefore(createElement('h2', userName), null);
                        commentElement.insertBefore(createElement('time', null, {
                            TIME_ATTR_SECONDS_TIMESTAMP: String(Date.parse(json.data.attributes['created-at']) / 1000)
                        }), null);
                        const button = createElement('button', 'Delete');
                        button.addEventListener('click', () => deleteComment(commentElement));
                        commentElement.insertBefore(button, null);

                        commentTextElement.value = null;
                        Events.getElement(SELECTOR_POST_COMMENTS).insertBefore(commentElement, null);
                    })
                    .catch((error: JsonApiError) => {
                        console.error('Adding comment failed.');
                        console.debug(JSON.stringify(error));
                    });
            });
        }
    }

    private runTimestampHumanize(): void {
        const update = () => {
            const timeElements = Events.getElements(SELECTOR_TIME);
            for (let i = 0; i < timeElements.length; ++i) {
                const timeElement = <HTMLTimeElement>timeElements[i];
                if (timeElement.hasAttribute(TIME_ATTR_SECONDS_TIMESTAMP) === true) {
                    const timestampInSeconds = parseInt(timeElement.getAttribute(TIME_ATTR_SECONDS_TIMESTAMP));
                    timeElement.textContent = timeago().format(timestampInSeconds * 1000);
                }
            }
        };

        update();

        const timeout = 1000 / 2;
        window.setInterval(update, timeout);
    }

    private runUpdateUI(): void {
        const update = () => {
            const newPostForm = Events.getElement(SELECTOR_POST_NEW);
            const newCommentForm = Events.getElement(SELECTOR_COMMENT_NEW);
            const posts = Events.getElements(SELECTOR_BOARD_POST);
            const comments = Events.getElements(SELECTOR_POST_COMMENT);
            if (this.application.hasAuthToken) {
                if (newPostForm !== null) {
                    newPostForm.classList.add(CLASS_IS_ACTIVE);
                }
                if (newCommentForm !== null) {
                    newCommentForm.classList.add(CLASS_IS_ACTIVE);
                }

                const userId = this.application.userId;
                const isAdmin = this.application.userHasScope(SCOPE_CAN_ADMIN_BOARDS);

                for (let i = 0; i < posts.length; ++i) {
                    const post = <HTMLButtonElement>posts[i];
                    const deleteButton: HTMLButtonElement = post.querySelector(SELECTOR_BOARD_POST__DELETE_BUTTON);
                    if (isAdmin === true || parseInt(post.getAttribute(BOARD_POST_ATTR_AUTHOR_ID)) === userId) {
                        deleteButton.classList.add(CLASS_IS_ACTIVE);
                    }
                }

                for (let i = 0; i < comments.length; ++i) {
                    const comment = <HTMLButtonElement>comments[i];
                    const deleteButton: HTMLButtonElement = comment.querySelector(SELECTOR_POST_COMMENT__DELETE_BUTTON);
                    if (isAdmin === true || parseInt(comment.getAttribute(POST_COMMENT_ATTR_AUTHOR_ID)) === userId) {
                        deleteButton.classList.add(CLASS_IS_ACTIVE);
                    }
                }
            } else {
                if (newPostForm !== null) {
                    newPostForm.classList.remove(CLASS_IS_ACTIVE);
                }
                if (newCommentForm !== null) {
                    newCommentForm.classList.remove(CLASS_IS_ACTIVE);
                }

                for (let i = 0; i < posts.length; ++i) {
                    const post = <HTMLButtonElement>posts[i];
                    const deleteButton: HTMLButtonElement = post.querySelector(SELECTOR_BOARD_POST__DELETE_BUTTON);
                    deleteButton.classList.remove(CLASS_IS_ACTIVE);
                }
                for (let i = 0; i < comments.length; ++i) {
                    const comment = <HTMLButtonElement>comments[i];
                    const deleteButton: HTMLButtonElement = comment.querySelector(SELECTOR_POST_COMMENT__DELETE_BUTTON);
                    deleteButton.classList.remove(CLASS_IS_ACTIVE);
                }
            }
        };

        update();
        const timeout = 1000 / 2;
        window.setInterval(update, timeout);
    }

    /**
     * Switch input form to 'read-only' mode.
     */
    private lockFormSubmit(): void {
        this.emailInput.readOnly = true;
        this.passwordInput.readOnly = true;
        this.loginButton.disabled = true;
    }

    /**
     * Switch input form from 'read-only' to normal mode.
     */
    private unlockFormSubmit(): void {
        this.emailInput.readOnly = false;
        this.passwordInput.readOnly = false;
        this.loginButton.disabled = false;
    }

    /**
     * @internal
     */
    private togglePopup(): void {
        if (this.isPopupVisible === true) {
            this.setPopupVisible(false);
        } else {
            this.setLoginLogoutVisibilityInPopup();
            this.setPopupVisible(true);
        }
    }

    /**
     * @returns {boolean}
     *
     * @internal
     */
    private get isPopupVisible(): boolean {
        return this.popup.classList.contains(CLASS_IS_ACTIVE) ||
            this.hamburger.classList.contains(CLASS_IS_ACTIVE);
    }

    /**
     * @param {boolean} isShow
     *
     * @internal
     */
    private setPopupVisible(isShow: boolean) {
        if (isShow === true) {
            this.hamburger.classList.add(CLASS_IS_ACTIVE);
            this.popup.classList.add(CLASS_IS_ACTIVE);
        } else {
            this.hamburger.classList.remove(CLASS_IS_ACTIVE);
            this.popup.classList.remove(CLASS_IS_ACTIVE);
        }
    }

    /**
     * @internal
     */
    private setErrorMessage(message: string): void {
        this.errorMessage.textContent = message;
    }

    /**
     * @internal
     */
    private clearErrorMessage(): void {
        this.errorMessage.textContent = null;
    }

    /**
     * @internal
     */
    private clearPassword(): HTMLInputElement {
        const element = this.passwordInput;
        element.value = null;

        return element;
    }

    /**
     * Setup visibility for logged in/out fields depending on valid auth token.
     */
    private setLoginLogoutVisibilityInPopup(): void {
        if (this.application.hasAuthToken === true) {
            this.loginSet.hidden = true;
            this.logoutSet.hidden = false;
        } else {
            this.loginSet.hidden = false;
            this.logoutSet.hidden = true;
        }
    }

    /**
     * @returns {Element}
     *
     * @internal
     */
    private get hamburger(): Element {
        if (this._hamburger === undefined) {
            this._hamburger = Events.getElement(SELECTOR_HAMBURGER);
        }

        return this._hamburger;
    }

    /**
     * @returns {Element}
     *
     * @internal
     */
    private get popup(): Element {
        if (this._popup === undefined) {
            this._popup = Events.getElement(SELECTOR_POPUP);
        }

        return this._popup;
    }

    /**
     * @returns {HTMLFieldSetElement}
     *
     * @internal
     */
    private get loginSet(): HTMLFieldSetElement {
        if (this._loginSet === undefined) {
            this._loginSet = <HTMLFieldSetElement>this.popup.querySelector(SELECTOR_POPUP_LOGIN_SET);
        }

        return this._loginSet;
    }

    /**
     * @returns {HTMLInputElement}
     *
     * @internal
     */
    private get emailInput(): HTMLInputElement {
        if (this._emailInput === undefined) {
            this._emailInput = <HTMLInputElement>this.loginSet.querySelector(SELECTOR_POPUP_LOGIN_SET__EMAIL);
        }

        return this._emailInput;
    }

    /**
     * @returns {HTMLInputElement}
     *
     * @internal
     */
    private get passwordInput(): HTMLInputElement {
        if (this._passwordInput === undefined) {
            this._passwordInput = <HTMLInputElement>this.loginSet.querySelector(SELECTOR_POPUP_LOGIN_SET__PASSWORD);
        }

        return this._passwordInput;
    }

    /**
     * @returns {HTMLInputElement}
     *
     * @internal
     */
    private get loginButton(): HTMLInputElement {
        if (this._loginButton === undefined) {
            this._loginButton = <HTMLInputElement>this.loginSet.querySelector(SELECTOR_POPUP_LOGIN_SET__BUTTON);
        }

        return this._loginButton;
    }

    /**
     * @returns {HTMLHeadElement}
     *
     * @internal
     */
    private get errorMessage(): HTMLHeadingElement {
        if (this._errorMessage === undefined) {
            this._errorMessage =
                <HTMLHeadingElement>this.loginSet.querySelector(SELECTOR_POPUP_LOGIN_SET__ERROR_MESSAGE);
        }

        return this._errorMessage;
    }

    /**
     * @returns {HTMLFieldSetElement}
     *
     * @internal
     */
    private get logoutSet(): HTMLFieldSetElement {
        if (this._logoutSet === undefined) {
            this._logoutSet = <HTMLFieldSetElement>this.popup.querySelector(SELECTOR_POPUP_LOGOUT_SET);
        }

        return this._logoutSet;
    }

    /**
     * @returns {HTMLInputElement}
     *
     * @internal
     */
    private get logoutButton(): HTMLInputElement {
        if (this._logoutButton === undefined) {
            this._logoutButton = <HTMLInputElement>this.logoutSet.querySelector(SELECTOR_POPUP_LOGOUT_SET__BUTTON);
        }

        return this._logoutButton;
    }

    /**
     * @param {string} selector
     *
     * @returns {Element}
     *
     * @internal
     */
    private static getElement(selector: string): Element | null {
        return window.document.querySelector(selector);
    }

    /**
     * @param {string} selector
     *
     * @returns {NodeListOf<Element>}
     *
     * @internal
     */
    private static getElements(selector: string): NodeListOf<Element> {
        return window.document.querySelectorAll(selector);
    }
}
