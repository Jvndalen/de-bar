import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import React, { useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface User {
    id: string;
    name: string;
    balance: number;
}

interface Product {
    id: string;
    name: string;
    price: number;
    quantity: number;
    image_url?: string;
}

interface TreatBalance {
    id: string;
    remaining_amount: number;
    initial_amount: number;
}

interface PageProps {
    products: Product[];
    user: User;
    activeTreatBalance?: TreatBalance;
    flash?: {
        success?: { message: string; transaction_id: string; type: string };
        error?: string;
        info?: string;
    };
}

export default function Dashboard() {
    const { products, user, activeTreatBalance, flash } = usePage<PageProps>().props;
    const [isScanning, setIsScanning] = useState(false);

    // Handle flash messages
    React.useEffect(() => {
        if (flash?.success) {
            const { message, transaction_id, type } = flash.success;

            toast.success(type === 'treat' ? 'Drankje gescand uit potje!' : 'Drankje gescand', {
                description: message,
                action: {
                    label: 'Maak ongedaan',
                    onClick: () => revertTransaction(transaction_id, type),
                },
            });
        }

        if (flash?.error) {
            toast.error(flash.error);
        }

        if (flash?.info) {
            toast.info(flash.info);
        }
    }, [flash]);

    const scanDrink = (user: User, product: Product) => {
        if (isScanning) return;

        setIsScanning(true);

        router.post(
            '/transactions/' + user.id,
            {
                product_id: product.id,
                is_drawer: false,
            },
            {
                preserveScroll: true,
                onFinish: () => setIsScanning(false),
            }
        );
    };

    const revertTransaction = (transactionId: string, type: string) => {
        router.post(
            `/transactions/${transactionId}/revert`,
            { type },
            {
                preserveScroll: true,
                onSuccess: () => {
                    toast.info('Transactie ongedaan gemaakt.');
                },
            }
        );
    };

    const formatCurrency = (cents: number) => {
        return new Intl.NumberFormat('nl-NL', {
            style: 'currency',
            currency: 'EUR',
        }).format(cents / 100);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="min-h-screen bg-gray-50 p-6">
                <div className="max-w-7xl mx-auto">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-gray-900">
                            Welkom, {user.name}!
                        </h1>
                        <div className="mt-4 flex gap-4">
                            <div className="bg-white rounded-lg shadow p-4">
                                <p className="text-sm text-gray-600">Hoofdsaldo</p>
                                <p className="text-2xl font-bold text-gray-900">
                                    {formatCurrency(user.balance)}
                                </p>
                            </div>

                            {activeTreatBalance && (
                                <div className="bg-green-50 rounded-lg shadow p-4 border border-green-200">
                                    <p className="text-sm text-green-800">Trakteer Potje 🎉</p>
                                    <p className="text-2xl font-bold text-green-900">
                                        {formatCurrency(activeTreatBalance.remaining_amount)}
                                    </p>
                                    <p className="text-xs text-green-600 mt-1">
                                        van {formatCurrency(activeTreatBalance.initial_amount)}
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Products Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {products.map((product) => (
                            <ProductCard
                                key={product.id}
                                product={product}
                                onScan={() => scanDrink(user, product)}
                                isScanning={isScanning}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}

interface ProductCardProps {
    product: Product;
    onScan: () => void;
    isScanning: boolean;
}

function ProductCard({ product, onScan, isScanning }: ProductCardProps) {
    const formatCurrency = (price: number) => {
        return new Intl.NumberFormat('nl-NL', {
            style: 'currency',
            currency: 'EUR',
        }).format(price);
    };

    return (
        <div className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            {product.image_url && (
                <img
                    src={product.image_url}
                    alt={product.name}
                    className="w-full h-48 object-cover"
                />
            )}

            <div className="p-4">
                <h3 className="text-lg font-semibold text-gray-900 mb-1">
                    {product.name}
                </h3>

                <div className="flex items-center justify-between mb-4">
                    <span className="text-2xl font-bold text-gray-900">
                        {formatCurrency(product.price)}
                    </span>
                    <span className="text-sm text-gray-600">
                        Voorraad: {product.quantity}
                    </span>
                </div>

                <button
                    onClick={onScan}
                    disabled={isScanning}
                    className="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors font-medium"
                >
                    {isScanning ? 'Bezig...' : 'Scannen'}
                </button>
            </div>
        </div>
    );
}
