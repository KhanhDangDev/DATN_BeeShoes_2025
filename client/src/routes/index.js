import { Suspense, lazy, useEffect } from 'react';
import { Navigate, useRoutes, useLocation } from 'react-router-dom';
// layouts
import MainLayout from '../layouts/main';
import DashboardLayout from '../layouts/dashboard';
import LogoOnlyLayout from '../layouts/LogoOnlyLayout';
// guards
import GuestGuard from '../guards/GuestGuard';
import AuthGuard from '../guards/AuthGuard';
import RoleBasedGuard from '../guards/RoleBasedGuard';
// config
import { PATH_AFTER_LOGIN } from '../config';
// components
import LoadingScreen from '../components/LoadingScreen';

// ----------------------------------------------------------------------

const ROLE_ADMIN = 'admin';
const ROLE_EMP = 'employee';

const Loadable = (Component) => (props) => {
  // eslint-disable-next-line react-hooks/rules-of-hooks
  const { pathname } = useLocation();

  return (
    <Suspense fallback={<LoadingScreen />}>
      <Component {...props} />
    </Suspense>
  );
};

export default function Router() {
  return useRoutes([
    {
      path: 'auth',
      children: [
        {
          path: 'login',
          element: (
            <GuestGuard>
              <Login />
            </GuestGuard>
          ),
        },
        {
          path: 'reset-password', element: (
            <GuestGuard>
              <ResetPassword />
            </GuestGuard>
          )
        },
      ],
    },

    // Dashboard Routes
    {
      path: 'dashboard',
      element: (
        <AuthGuard>
          <DashboardLayout />
        </AuthGuard>
      ),
      children: [
        { element: <Navigate to={PATH_AFTER_LOGIN} replace />, index: true },
        // {
        //   path: 'app', element: (
        //     <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //       <GeneralEcommerce />
        //     </RoleBasedGuard>
        //   )
        // },
        // {
        //   path: 'bill',
        //   children: [
        //     { element: <Navigate to="/dashboard/bill/list" replace />, index: true },
        //     { path: 'list', element: <BillList /> },
        //     { path: ':id/edit', element: <BillDetails /> },
        //   ],
        // },

        {
          path: 'product',
          children: [
            { element: <Navigate to="/dashboard/product/list" replace />, index: true },
            { path: 'list', element: <ProductList /> },
            {
              path: 'new', element: (
                <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
                  <ProductCreateEdit />
                </RoleBasedGuard>
              )
            },
            {
              path: ':id/edit', element: (
                <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
                  <ProductCreateEdit />
                </RoleBasedGuard>
              )
            },
          ],
        },

        {
          path: 'attribute',
          children: [
            {
              path: 'color',
              children: [
                { element: <Navigate to="/dashboard/attribute/color/list" replace />, index: true },
                { path: 'list', element: <ColorList /> },
              ]
            },

            {
              path: 'category',
              children: [
                { element: <Navigate to="/dashboard/attribute/category/list" replace />, index: true },
                { path: 'list', element: <CategoryList /> },
              ]
            },

            {
              path: 'brand',
              children: [
                { element: <Navigate to="/dashboard/attribute/brand/list" replace />, index: true },
                { path: 'list', element: <BrandList /> },
              ]
            },

            {
              path: 'size',
              children: [
                { element: <Navigate to="/dashboard/attribute/size/list" replace />, index: true },
                { path: 'list', element: <SizeList /> },
              ]
            },
          ],
        },


        // {
        //   path: 'account',
        //   children: [
        //     {
        //       path: 'customer',
        //       children: [
        //         { element: <Navigate to="/dashboard/account/customer/list" replace />, index: true },
        //         { path: 'list', element: <CustomerList /> },
        //         { path: ':id', element: <CustomerNewEdit /> },
        //         { path: ':id/edit', element: <CustomerNewEdit /> },
        //       ]
        //     },
        //     {
        //       path: 'employee',
        //       children: [
        //         { element: <Navigate to="/dashboard/account/employee/list" replace />, index: true },
        //         {
        //           path: 'list', element: (
        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <EmployeeList />
        //             </RoleBasedGuard>
        //           )
        //         },
        //         {
        //           path: ':id', element: (

        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <EmployeeNewEdit />
        //             </RoleBasedGuard>
        //           )
        //         },
        //         {
        //           path: ':id/edit', element: (

        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <EmployeeNewEdit />
        //             </RoleBasedGuard>
        //           )
        //         },
        //       ],
        //     },
        //   ],
        // },

        // {
        //   path: 'discount',
        //   children: [
        //     {
        //       path: 'voucher',
        //       children: [
        //         { element: <Navigate to="/dashboard/discount/voucher/list" replace />, index: true },
        //         {
        //           path: 'list', element: (
        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <VoucherList />
        //             </RoleBasedGuard>
        //           )
        //         },
        //         // { path: ':id', element: <InvoiceDetails /> },
        //         {
        //           path: ':id/edit', element: (

        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <VoucherCreateEdit />
        //             </RoleBasedGuard>
        //           )
        //         },
        //         {
        //           path: 'new', element: (

        //             <RoleBasedGuard accessibleRoles={[ROLE_ADMIN]}>
        //               <VoucherCreateEdit />
        //             </RoleBasedGuard>
        //           )
        //         },
        //       ],
        //     },
        //   ],
        // },
      ],
    },

    // Main Routes
    {
      path: '*',
      element: <LogoOnlyLayout />,
      children: [
        { path: 'coming-soon', element: <ComingSoon /> },
        { path: 'maintenance', element: <Maintenance /> },
        { path: 'pricing', element: <Pricing /> },
        { path: 'payment', element: <Payment /> },
        { path: '500', element: <Page500 /> },
        { path: '404', element: <NotFound /> },
        { path: '*', element: <Navigate to="/404" replace /> },
      ],
    },
    {
      path: '/',
      element: <MainLayout />,
      children: [
        { element: <HomePage />, index: true },
        { path: 'about-us', element: <About /> },
        { path: 'contact-us', element: <Contact /> },
        { path: 'faqs', element: <Faqs /> },
      ],
    },
    { path: '*', element: <Navigate to="/404" replace /> },
  ]);
}

// VOUCHER
// const VoucherList = Loadable(lazy(() => import('../pages/dashboard/voucher/list/VoucherList')))
// const VoucherCreateEdit = Loadable(lazy(() => import('../pages/dashboard/voucher/new-edit/VoucherNewEdit')))
// CUSTOMER
// const CustomerList = Loadable(lazy(() => import('../pages/dashboard/customer/list/CustomerList')))
// const CustomerNewEdit = Loadable(lazy(() => import('../pages/dashboard/customer/new-edit/CustomerNewEdit')))
// Employee
// const EmployeeList = Loadable(lazy(() => import('../pages/dashboard/employee/list/EmployeeList')))
// const EmployeeNewEdit = Loadable(lazy(() => import('../pages/dashboard/employee/new-edit/EmployeeNewEdit')))
// PRODUCT
const ProductList = Loadable(lazy(() => import('../pages/dashboard/product/list/ProductList')))
const ProductCreateEdit = Loadable(lazy(() => import('../pages/dashboard/product/new-edit/ProductNewEdit')))
// ATTRIBUTE
const ColorList = Loadable(lazy(() => import('../pages/dashboard/attributes/color/list/ColorList')))
const CategoryList = Loadable(lazy(() => import('../pages/dashboard/attributes/category/list/CategoryList')))
const BrandList = Loadable(lazy(() => import('../pages/dashboard/attributes/brand/list/BrandList')))
const SizeList = Loadable(lazy(() => import('../pages/dashboard/attributes/size/list/SizeList')))
// BILL
// const BillList = Loadable(lazy(() => import('../pages/dashboard/order/list/BillList')))
// const BillDetails = Loadable(lazy(() => import('../pages/dashboard/order/details/BillDetails')))

// AUTHENTICATION
const Login = Loadable(lazy(() => import('../pages/auth/Login')));
const ResetPassword = Loadable(lazy(() => import('../pages/auth/ResetPassword')));

// MAIN
const HomePage = Loadable(lazy(() => import('../pages/Home')));
const About = Loadable(lazy(() => import('../pages/About')));
const Contact = Loadable(lazy(() => import('../pages/Contact')));
const Faqs = Loadable(lazy(() => import('../pages/Faqs')));
const ComingSoon = Loadable(lazy(() => import('../pages/ComingSoon')));
const Maintenance = Loadable(lazy(() => import('../pages/Maintenance')));
const Pricing = Loadable(lazy(() => import('../pages/Pricing')));
const Payment = Loadable(lazy(() => import('../pages/Payment')));
const Page500 = Loadable(lazy(() => import('../pages/Page500')));
const NotFound = Loadable(lazy(() => import('../pages/Page404')));

// const GeneralEcommerce = Loadable(lazy(() => import('../pages/dashboard/GeneralEcommerce')));
