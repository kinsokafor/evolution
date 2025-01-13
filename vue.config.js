module.exports = {
    chainWebpack: (config) => {
      config.module
        .rule('vue')
        .use('vue-loader')
        .tap((options) => {
          options.compilerOptions = {
            isCustomElement: (tag) =>
              tag === 'swiper-container' || tag === 'swiper-slide',
          };
          return options;
        });
    },
  };
  