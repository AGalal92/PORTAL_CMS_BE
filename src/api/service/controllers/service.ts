/**
 * service controller
 */

import { factories } from '@strapi/strapi';

export default factories.createCoreController('api::service.service', ({ strapi }) => ({
  async find(ctx) {
    ctx.query = {
      ...ctx.query,
      populate: ['image', 'icon'], // Populate only 'image' and 'icon'
    };
    const { data, meta } = await super.find(ctx);
    const transformedData = data.map(item => {
      // Extract `url` from `icon` and `image`
      const icon = (item.icon || []).map(iconItem => iconItem.url || null);
      const image = (item.image || []).map(imageItem => imageItem.url || null);

      return {
        ...item,
        image, // Include extracted image URLs
        icon,  // Include extracted icon URLs
      };
    });

    return { data: transformedData, meta };
  },
}));
