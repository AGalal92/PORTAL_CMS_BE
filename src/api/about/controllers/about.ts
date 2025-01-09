/**
 * about controller
 */
import { factories } from '@strapi/strapi';

export default factories.createCoreController('api::about.about', ({ strapi }) => ({
  async find(ctx) {
    ctx.query = {
      ...ctx.query,
      populate: ['image','list.logo'],
    };
    const { data, meta } = await super.find(ctx);
    const transformedData = data.map(item => {
      const list = (item.list || []).map(listItem => ({
        ...listItem,
        logo: (listItem.logo || []).map(logoItem => logoItem.url || null), // Extract `url` from `logo`
      }));
      const image = (item.image || []).map(imageItem => imageItem.url || null); // Extract `url` from `image`

      return {
        ...item,
        list,
        image,
      };
    });

    return { data: transformedData, meta };
  },
}));
