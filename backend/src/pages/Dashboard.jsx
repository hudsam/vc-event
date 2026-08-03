import React, { useEffect, useState } from 'react';
import { eventService } from '../services/api.js';
import { Calendar, CheckCircle2, FileText, Archive, ChevronRight, AlertCircle } from 'lucide-react';
import { Link } from 'react-router-dom';

const Dashboard = () => {
  const [stats, setStats] = useState({ total: 0, draft: 0, published: 0, archived: 0 });
  const [latestEvents, setLatestEvents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);
        setError('');
        // Fetch all events to compute stats locally and grab latest
        const response = await eventService.getAll();
        if (response.status === 'success') {
          const events = response.data || [];
          
          const computedStats = {
            total: events.length,
            draft: events.filter(e => e.status === 'draft').length,
            published: events.filter(e => e.status === 'published').length,
            archived: events.filter(e => e.status === 'archived').length,
          };
          setStats(computedStats);

          // Get latest 5 events based on start_date
          const sorted = [...events].sort((a, b) => new Date(b.start_date) - new Date(a.start_date));
          setLatestEvents(sorted.slice(0, 5));
        } else {
          setError('Failed to load dashboard data.');
        }
      } catch (err) {
        console.error(err);
        setError('Error connecting to the API server. Please check your config.');
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  const statCards = [
    { name: 'Total Events', value: stats.total, icon: Calendar, color: 'from-blue-600 to-indigo-600', shadow: 'shadow-blue-500/10' },
    { name: 'Published', value: stats.published, icon: CheckCircle2, color: 'from-emerald-600 to-teal-600', shadow: 'shadow-emerald-500/10' },
    { name: 'Drafts', value: stats.draft, icon: FileText, color: 'from-amber-600 to-orange-600', shadow: 'shadow-amber-500/10' },
    { name: 'Archived', value: stats.archived, icon: Archive, color: 'from-slate-600 to-zinc-600', shadow: 'shadow-slate-500/10' },
  ];

  if (loading) {
    return (
      <div className="space-y-8 animate-pulse">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
          {[1, 2, 3, 4].map((n) => (
            <div key={n} className="h-32 bg-slate-900 border border-slate-800 rounded-2xl"></div>
          ))}
        </div>
        <div className="bg-slate-900 border border-slate-800 rounded-2xl h-96"></div>
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {error && (
        <div className="p-4 bg-amber-950/30 border border-amber-900/30 text-amber-400 rounded-2xl flex items-center gap-3 text-sm">
          <AlertCircle className="shrink-0" size={18} />
          <span>{error} (Using local calculations)</span>
        </div>
      )}

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        {statCards.map((card) => {
          const Icon = card.icon;
          return (
            <div
              key={card.name}
              className={`p-6 bg-slate-900 border border-slate-800/80 rounded-2xl shadow-lg ${card.shadow} flex items-center justify-between hover:border-slate-700 transition-all`}
            >
              <div>
                <p className="text-slate-400 text-sm font-semibold">{card.name}</p>
                <p className="text-3xl font-extrabold mt-2 text-white">{card.value}</p>
              </div>
              <div className={`w-12 h-12 rounded-xl bg-gradient-to-br ${card.color} flex items-center justify-center text-white shadow-md`}>
                <Icon size={22} />
              </div>
            </div>
          );
        })}
      </div>

      {/* Latest Events */}
      <div className="bg-slate-900 border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden">
        <div className="px-6 py-5 border-b border-slate-800/85 flex items-center justify-between">
          <h2 className="text-base font-bold text-slate-200">Latest Events</h2>
          <Link to="/events" className="text-xs text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1 transition-colors">
            View All Events <ChevronRight size={14} />
          </Link>
        </div>
        <div className="overflow-x-auto">
          {latestEvents.length === 0 ? (
            <div className="px-6 py-12 text-center text-slate-500 text-sm">
              No events found. Start by creating a new event!
            </div>
          ) : (
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-slate-800/60 bg-slate-950/20 text-slate-400 text-xs font-bold uppercase tracking-wider">
                  <th className="px-6 py-4">Title</th>
                  <th className="px-6 py-4">Category</th>
                  <th className="px-6 py-4">Venue</th>
                  <th className="px-6 py-4">Start Date</th>
                  <th className="px-6 py-4">Status</th>
                  <th className="px-6 py-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/50">
                {latestEvents.map((event) => (
                  <tr key={event.id} className="hover:bg-slate-800/20 transition-colors text-sm">
                    <td className="px-6 py-4 font-semibold text-slate-200">{event.title}</td>
                    <td className="px-6 py-4 text-slate-400">{event.category}</td>
                    <td className="px-6 py-4 text-slate-400">{event.venue}</td>
                    <td className="px-6 py-4 text-slate-400">
                      {event.start_date ? new Date(event.start_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                      }) : '-'}
                    </td>
                    <td className="px-6 py-4">
                      <span
                        className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border ${
                          event.status === 'published'
                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                            : event.status === 'archived'
                            ? 'bg-slate-500/10 text-slate-400 border-slate-500/20'
                            : 'bg-amber-500/10 text-amber-400 border-amber-500/20'
                        }`}
                      >
                        {event.status}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <Link
                        to={`/events/${event.id}`}
                        className="inline-flex items-center gap-1 text-indigo-400 hover:text-indigo-300 font-semibold transition-colors"
                      >
                        Detail <ChevronRight size={14} />
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
