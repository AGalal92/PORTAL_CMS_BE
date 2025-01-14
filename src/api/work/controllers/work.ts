/**
 * work controller
 */

import { factories } from '@strapi/strapi';

export default factories.createCoreController('api::work.work', ({ strapi }) => ({
  async find(ctx) {
    // Check if the query contains the 'section' parameter
    const isSectionQuery = ctx.query.section === 'true'; // Assuming you expect `?section=true`
    console.log("🚀 ~ find ~ isSectionQuery:", isSectionQuery);

    // Modify the query based on the presence of the 'section' parameter
    if (isSectionQuery) {
      ctx.query = {
        ...ctx.query,
        fields: ['name', 'slug'], // Include only 'name' and 'slug'
      };
    } else {
      ctx.query = {
        ...ctx.query,
        populate: ['image', 'link', 'tag', 'project_image'], // Default population for other queries
      };
    }

    // Call the default `find` method
    const { data, meta } = await super.find(ctx);

    // If it's the 'section' query, no additional transformation needed
    if (isSectionQuery) {
      return { data, meta };
    }

    // Default transformation for non-section queries
    const transformedData = data.map((item) => {
      const image = (item.image || []).map((imageItem) => imageItem.url || null);
      const project_image = (item.project_image || []).map(
        (projectImageItem) => projectImageItem.url || null
      );

      return {
        ...item,
        project_image, // Include only the URLs for project images
        image, // Include extracted image URLs
      };
    });

    return { data: transformedData, meta };
  },
}));
