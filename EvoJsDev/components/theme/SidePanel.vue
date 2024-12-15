<!-- Use preprocessors via the lang attribute! e.g. <template lang="pug"> -->
    <template>
        <div class="drawer-container">
          <div class="drawer" :class="[direction, displayStatus ? 'show' : '']">
            <div class="handle">
              <div class="caption" @click="show">
                <svg viewBox="0 0 448 512" width="100" title="angle-double-left">
                    <path d="M223.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L319.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L393.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34zm-192 34l136 136c9.4 9.4 24.6 9.4 33.9 0l22.6-22.6c9.4-9.4 9.4-24.6 0-33.9L127.9 256l96.4-96.4c9.4-9.4 9.4-24.6 0-33.9L201.7 103c-9.4-9.4-24.6-9.4-33.9 0l-136 136c-9.5 9.4-9.5 24.6-.1 34z" />
                </svg>
              </div>
            </div>
            <div class="body">
              <div class="content">
                <slot></slot>
              </div>
            </div>
          </div>
        </div>
      </template>
      
      <script setup>
        import {ref} from 'vue'
        import '/color-scheme.css'
        const displayStatus = ref(false)
        const props = defineProps({
          direction: {
            type: String,
            default: "right"
          }
        })
        const show = () => {
          displayStatus.value = !displayStatus.value
        }
      </script>

      <style lang="scss">
        html {
          scrollbar-width: thin;
        }
      </style>
      
      <style lang="scss" scoped>
        .drawer-container {
          height: 0vh;
          width: auto; //100vw;
          position: fixed;
          top: 0;
          overflow: hidden;
        }
        .drawer {
          display: flex;
          box-sizing: border-box;
          position: fixed;
          height: 100vh;
          width: 100vw;
          top: 0;
        }
        .drawer > div {
          display: inline-block;
        }
        .drawer .handle {
          box-sizing: border-box;
          position: relative;
          transform: translate(0%, 50%);
          top: 0%;
          right: 0px;
          width:20vw;
          transition: width 1s linear;
        }
        .drawer .handle .caption {
          position: absolute;
          transform: translate(0%, -50%);
          background: var(--color1);
          cursor: pointer;
          text-transform: uppercase;
        }
        .drawer .handle .caption svg {
          fill: var(--highlight1);
          width: 15px;
          padding: 30px 8px;
          box-sizing: content-box;
        }
        .drawer .body {
          box-sizing: border-box;
          height: 100vh;
          width: 80vw;
          background-color: var(--highlight1);
          position: relative;
        }
        .drawer .body .content {
          width: 90%;
          height: 90%;
          position: absolute;
          transform: translate(50%, -50%);
          right: 50%;
          top: 50%;
          overflow: auto; /* Keeps the scrolling functionality */
          scrollbar-width: none; /* Firefox */
        }
      .drawer::-webkit-scrollbar {
        display: none; /* Chrome, Safari, and Edge */
      }
        .drawer:not(.show) .handle {
            width: 0;
        }
        .drawer.right {
          left: 100vw;//calc(100vw - 12px);
          transition: left 0.5s linear;
        }
        .drawer.left {
          right: 100vw;
          transition: right 0.5s linear;
          flex-direction: row-reverse;
        }
        .drawer.right .handle .caption {
          right: -1px;
          border-radius: 0 0 0px 10px;
        }
        .drawer.left .handle .caption {
          left: -1px;
          border-radius: 0 0 10px 0px;
        }
        .drawer.right.show {
          left: 0vw;
        }
        .drawer.left.show {
          right: 0vw;
        }
        .drawer.left .handle .caption svg,
        .drawer.show.right .handle .caption svg{
          transform: rotate(180deg);
        }
        .drawer.right .handle .caption svg,
        .drawer.show.left .handle .caption svg {
          transform: rotate(0deg);
        }
        .drawer.right.show .body {
          box-shadow: -5px 10px 5px rgba(0, 0, 0, 0.1);
        }
        .drawer.left.show .body {
          box-shadow: 5px 10px 5px rgba(0, 0, 0, 0.1);
        }
      </style>