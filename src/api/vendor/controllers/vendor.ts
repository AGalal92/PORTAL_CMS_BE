/**
 * vendor controller
 */

import { factories } from '@strapi/strapi'

// export default factories.createCoreController('api::vendor.vendor');
export default factories.createCoreController('api::vendor.vendor', ({ strapi }) => ({
    async find(ctx) {
      ctx.query = {
        ...ctx.query,
        populate: ['image'], // Populate only 'image' 
      };
      const { data, meta } = await super.find(ctx);
      const transformedData = data.map(item => {
        // Extract `url` from `icon` and `image`
        const image = (item.image || []).map(imageItem => imageItem.url || null);
  
        return {
          ...item,
          image // Include extracted image URLs
        };
      });
  
      return { data: transformedData, meta };
    },
  }));
