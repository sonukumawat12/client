/*
 *
 * Customer Application Vue routes
 *
 *
 */

const routes = [
    { name: "home", path: "/", component: () => import("./views/Home/Index") },
    {
        name: "tickets",
        meta: { title: "My tickets" },
        path: "/tickets",
        component: () => import("./views/ticket/List"),
    },
    {
        name: "tickets.new",
        meta: { title: "New ticket" },
        path: "/tickets/new",
        component: () => import("./views/ticket/New"),
    },
    {
        name: "tickets.view",
        meta: { title: "Ticket details" },
        path: "/tickets/:id",
        props: true,
        component: () => import("./views/ticket/View"),
    },
];

routes.push({
    path: "/:pathMatch(.*)*",
    component: () => import("./../../common/components/NotFound"),
});

export default routes;
