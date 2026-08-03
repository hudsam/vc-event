import React, { createContext, useContext, useState, useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, Link, useLocation, useNavigate } from 'react-router-dom';
import { Calendar, LayoutDashboard, LogOut, User as UserIcon, Menu } from 'lucide-react';

// Create Auth Context
const AuthContext = createContext(null);

export const useAuth = () => useContext(AuthContext);

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  const { user, loading } = useAuth();
  if (loading) return <div className="flex items-center justify-center min-h-screen bg-slate-900 text-white">Loading...</div>;
  if (!user) return <Navigate to="/login" replace />;
  return children;
};

// Main Layout Component
const Layout = ({ children }) => {
  const { user, logout } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();

  const menuItems = [
    { name: 'Dashboard', path: '/dashboard', icon: LayoutDashboard },
    { name: 'Events', path: '/events', icon: Calendar },
  ];

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className="min-h-screen bg-slate-950 text-slate-100 flex font-sans">
      {/* Sidebar */}
      <aside className="w-64 bg-slate-900 border-r border-slate-800 flex flex-col fixed top-0 bottom-0 left-0 z-50">
        <div className="p-6 text-2xl font-extrabold text-center tracking-tight bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent border-b border-slate-800">
          Maxy Event
        </div>
        <nav className="flex-1 py-6 space-y-1 px-4">
          {menuItems.map((item) => {
            const Icon = item.icon;
            const isActive = location.pathname === item.path || (item.path === '/events' && location.pathname.startsWith('/events'));
            return (
              <Link
                key={item.name}
                to={item.path}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all ${
                  isActive
                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20'
                    : 'text-slate-400 hover:text-white hover:bg-slate-800/50'
                }`}
              >
                <Icon size={20} />
                {item.name}
              </Link>
            );
          })}
        </nav>
        <div className="p-4 border-t border-slate-800">
          <div className="flex items-center gap-3 px-4 py-3 mb-3">
            <div className="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center">
              <UserIcon size={16} className="text-slate-400" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold truncate">{user?.name || 'Admin'}</p>
              <p className="text-xs text-slate-500 truncate">{user?.email}</p>
            </div>
          </div>
          <button
            onClick={handleLogout}
            className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-950/30 text-red-400 border border-red-900/30 hover:bg-red-900/40 transition-all font-semibold"
          >
            <LogOut size={16} />
            Logout
          </button>
        </div>
      </aside>

      {/* Main Content Area */}
      <div className="flex-1 pl-64 min-h-screen flex flex-col">
        {/* Topbar */}
        <header className="h-16 bg-slate-900/50 backdrop-blur-md border-b border-slate-800/80 px-8 flex items-center justify-between sticky top-0 z-40">
          <h1 className="text-lg font-bold text-slate-200">
            {location.pathname === '/dashboard'
              ? 'Dashboard Overview'
              : location.pathname === '/events'
              ? 'Manage Events'
              : location.pathname === '/events/create'
              ? 'Create New Event'
              : location.pathname.includes('/edit')
              ? 'Edit Event'
              : 'Event Details'}
          </h1>
          <div className="flex items-center gap-3">
            <span className="text-xs bg-indigo-500/10 text-indigo-400 px-3 py-1 rounded-full border border-indigo-500/20 font-medium">
              Administrator
            </span>
          </div>
        </header>

        {/* Content Body */}
        <main className="flex-1 p-8 bg-slate-950">
          {children}
        </main>
      </div>
    </div>
  );
};

// Pages imports placeholder (we will create these files next)
import Login from './pages/Login.jsx';
import Dashboard from './pages/Dashboard.jsx';
import EventList from './pages/EventList.jsx';
import EventDetail from './pages/EventDetail.jsx';
import EventForm from './pages/EventForm.jsx';

const App = () => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
      setUser(JSON.parse(savedUser));
    }
    setLoading(false);
  }, []);

  const login = (userData) => {
    setUser(userData);
    localStorage.setItem('user', JSON.stringify(userData));
    localStorage.setItem('token', 'dummy-token'); // Simple session token
  };

  const logout = () => {
    setUser(null);
    localStorage.removeItem('user');
    localStorage.removeItem('token');
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout }}>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={user ? <Navigate to="/dashboard" replace /> : <Login />} />
          
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <Layout>
                  <Dashboard />
                </Layout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/events"
            element={
              <ProtectedRoute>
                <Layout>
                  <EventList />
                </Layout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/events/create"
            element={
              <ProtectedRoute>
                <Layout>
                  <EventForm />
                </Layout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/events/:id"
            element={
              <ProtectedRoute>
                <Layout>
                  <EventDetail />
                </Layout>
              </ProtectedRoute>
            }
          />
          <Route
            path="/events/:id/edit"
            element={
              <ProtectedRoute>
                <Layout>
                  <EventForm />
                </Layout>
              </ProtectedRoute>
            }
          />
          
          <Route path="*" element={<Navigate to="/dashboard" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthContext.Provider>
  );
};

export default App;
