import type { Metadata } from 'next';
import './globals.css';
export const metadata: Metadata = { title: 'PPID Pengadilan Negeri Sukadana', description: 'Portal resmi PPID Pengadilan Negeri Sukadana untuk layanan informasi publik yang transparan dan akuntabel.', keywords: ['PPID', 'Pengadilan Negeri Sukadana', 'informasi publik'] };
export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) { return <html lang="id"><body>{children}</body></html>; }
