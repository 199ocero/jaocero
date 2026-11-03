/**
 * <robot-head>: the AI assistant's avatar, an animated robot from the robot-heads package
 * (https://www.npmjs.com/package/robot-heads), usable from Blade and Alpine.
 *
 *   <robot-head shape="circle" state="thinking" size="36" color="#2b49a3" antenna="false"></robot-head>
 *
 * Attributes: shape, state, size, color, trim-color, screen-color, antenna, interactive, floor-shadow, paused.
 * robot-heads is a React component, so React is loaded on demand the first time a head appears. It renders
 * into a shadow root so Livewire's DOM morphing never touches it; add wire:ignore when Alpine binds attributes.
 */
let modules = null

function loadModules() {
    modules ??= Promise.all([import('react'), import('react-dom'), import('react-dom/client'), import('robot-heads')]).then(
        ([react, reactDom, reactDomClient, robotHeads]) => ({
            createElement: react.createElement,
            flushSync: reactDom.flushSync,
            createRoot: reactDomClient.createRoot,
            RobotHead: robotHeads.RobotHead,
        }),
    )

    return modules
}

const BOOLEAN_ATTRIBUTES = {
    antenna: 'antenna',
    interactive: 'interactive',
    'floor-shadow': 'floorShadow',
    paused: 'paused',
}

const STRING_ATTRIBUTES = {
    shape: 'shape',
    state: 'state',
    color: 'color',
    'trim-color': 'trimColor',
    'screen-color': 'screenColor',
}

class RobotHeadElement extends HTMLElement {
    static observedAttributes = ['size', ...Object.keys(STRING_ATTRIBUTES), ...Object.keys(BOOLEAN_ATTRIBUTES)]

    root = null

    connectedCallback() {
        if (this.root) {
            this.render()

            return
        }

        loadModules().then(({ createRoot }) => {
            if (!this.isConnected || this.root) return

            this.root = createRoot(this.container())
            this.render()
        })
    }

    container() {
        if (!this.shadowRoot) {
            const style = document.createElement('style')
            style.textContent = ':host { display: block }'

            this.attachShadow({ mode: 'open' }).append(style, document.createElement('span'))
        }

        return this.shadowRoot.lastElementChild
    }

    disconnectedCallback() {
        // Livewire's @persist and wire:navigate move elements, which disconnects and reconnects them right away.
        queueMicrotask(() => {
            if (!this.isConnected && this.root) {
                this.root.unmount()
                this.root = null
            }
        })
    }

    attributeChangedCallback() {
        this.render()
    }

    props() {
        const props = { size: Number(this.getAttribute('size')) || 36 }

        for (const [attribute, prop] of Object.entries(STRING_ATTRIBUTES)) {
            const value = this.getAttribute(attribute)

            if (value) props[prop] = value
        }

        for (const [attribute, prop] of Object.entries(BOOLEAN_ATTRIBUTES)) {
            if (this.hasAttribute(attribute)) props[prop] = this.getAttribute(attribute) !== 'false'
        }

        return props
    }

    render() {
        if (!this.root) return

        // flushSync commits right away, so a head added by a Livewire morph is drawn in the same frame instead of popping in.
        loadModules().then(({ createElement, flushSync, RobotHead }) => {
            flushSync(() => this.root?.render(createElement(RobotHead, { ...this.props(), 'aria-hidden': true })))
        })
    }
}

if (!customElements.get('robot-head')) {
    customElements.define('robot-head', RobotHeadElement)
}
