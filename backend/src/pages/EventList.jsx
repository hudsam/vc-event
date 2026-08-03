import React, { useEffect, useState } from 'react';
import { eventService } from '../services/api.js';
import { Plus, Eye, Edit2, Trash2, Globe, Archive, FileText, CheckCircle, RefreshCw, AlertCircle } from 'lucide-react';
import { Link, useNavigate } from 'react-router-dom';

const EventList = () => {
  const navigate = useNavigate();
  const [events, setEvents] = useState([]);
  const [activeTab, setActiveTab] = useState('all'); // all, draft, published, archived
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  const fetchEvents = async () => {
    try {
      setLoading(true);
      setError('');
      const statusParam = activeTab === 'all' ? null : activeTab;
      const response = await eventService.getAll(statusParam);
      if (response.status === 'success') {
        setEvents(response.data || []);
      } else {
        setError('Failed to fetch events from API.');
      }
    } catch (err) {
      console.error(err);
      setError('Failed to connect to API server. Please check VITE_API_URL or run Python api.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEvents();
  }, [activeTab]);

  const handleDelete = async (id) => {
    if (!window.confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
      return;
    }
    try {
      setError('');
      setSuccessMsg('');
      const response = await eventService.delete(id);
      if (response.status === 'success') {
        setSuccessMsg('Event deleted successfully.');
        fetchEvents();
      } else {
        setError(response.message || 'Failed to delete event.');
      }
    } catch (err) {
      console.error(err);
      setError(err.response?.data?.message || 'Error occurred while deleting event.');
    }
  };

  const handleStatusChange = async (event, newStatus) => {
    try {
      setError('');
      setSuccessMsg('');

      // Validation check for publish
      if (newStatus === 'published') {
        if (!event.title || !event.category || !event.venue || !event.start_date || !event.thumbnail || !event.banner) {
          setError('Event must have all required fields before publishing.');
          return;
        }
      }

      const updatedData = {
        ...event,
        status: newStatus,
        // Backend API expects formatted string or ISO string for date
        start_date: event.start_date,
        end_date: event.end_date
      };

      const response = await eventService.update(event.id, updatedData);
      if (response.status === 'success') {
        setSuccessMsg(`Event status updated to ${newStatus} successfully.`);
        fetchEvents();
      } else {
        setError(response.message || 'Failed to update event status.');
      }
    } catch (err) {
      console.error(err);
      setError(err.response?.data?.message || 'Error occurred while updating event status.');
    }
  };

  const tabs = [
    { id: 'all', label: 'All Events' },
    { id: 'draft', label: 'Drafts' },
    { id: 'published', label: 'Published' },
    { id: 'archived', label: 'Archived' },
  ];

  return (
    <div className="space-y-6">
      {/* Header action */}
      <div className="flex justify-between items-center">
        <div>
          <p className="text-sm text-slate-400">Total {events.length} events found</p>
        </div>
        <Link
          to="/events/create"
          className="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-lg shadow-indigo-600/20 font-bold transition-all text-sm"
        >
          <Plus size={18} />
          Create Event
        </Link>
      </div>

      {/* Notifications */}
      {successMsg && (
        <div className="p-4 bg-emerald-950/30 border border-emerald-900/30 text-emerald-400 rounded-xl flex items-center gap-3 text-sm">
          <CheckCircle size={18} />
          <span>{successMsg}</span>
        </div>
      )}
      {error && (
        <div className="p-4 bg-red-950/30 border border-red-900/30 text-red-400 rounded-xl flex items-center gap-3 text-sm">
          <AlertCircle size={18} />
          <span>{error}</span>
        </div>
      )}

      {/* Tabs */}
      <div className="flex border-b border-slate-800">
        {tabs.map((tab) => (
          <button
            key={tab.id}
            onClick={() => setActiveTab(tab.id)}
            className={`px-6 py-3 font-semibold text-sm border-b-2 transition-all ${
              activeTab === tab.id
                ? 'border-indigo-500 text-indigo-400'
                : 'border-transparent text-slate-400 hover:text-slate-200'
            }`}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {/* Table Container */}
      <div className="bg-slate-900 border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden">
        {loading ? (
          <div className="p-12 text-center text-slate-500 text-sm animate-pulse space-y-4">
            <RefreshCw size={24} className="animate-spin mx-auto text-indigo-500" />
            <p>Fetching events...</p>
          </div>
        ) : events.length === 0 ? (
          <div className="p-12 text-center text-slate-500 text-sm">
            No events found in this category.
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-slate-800/60 bg-slate-950/20 text-slate-400 text-xs font-bold uppercase tracking-wider">
                  <th className="px-6 py-4">Event info</th>
                  <th className="px-6 py-4">Category</th>
                  <th className="px-6 py-4">Venue</th>
                  <th className="px-6 py-4">Date</th>
                  <th className="px-6 py-4">Status</th>
                  <th className="px-6 py-4 text-center">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-800/50">
                {events.map((event) => (
                  <tr key={event.id} className="hover:bg-slate-800/10 transition-colors text-sm">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        {event.thumbnail && (
                          <img
                            src={event.thumbnail}
                            alt={event.title}
                            className="w-10 h-10 rounded-lg object-cover bg-slate-800"
                            onError={(e) => {
                              e.target.style.display = 'none';
                            }}
                          />
                        )}
                        <div>
                          <p className="font-bold text-slate-200">{event.title}</p>
                          <p className="text-xs text-slate-500 truncate max-w-xs">{event.organizer}</p>
                        </div>
                      </div>
                    </td>
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
                    <td className="px-6 py-4">
                      <div className="flex items-center justify-center gap-2">
                        {/* Action buttons */}
                        <Link
                          to={`/events/${event.id}`}
                          title="View Detail"
                          className="p-2 text-slate-400 hover:text-indigo-400 hover:bg-slate-800/60 rounded-lg transition-all"
                        >
                          <Eye size={16} />
                        </Link>
                        <Link
                          to={`/events/${event.id}/edit`}
                          title="Edit"
                          className="p-2 text-slate-400 hover:text-amber-400 hover:bg-slate-800/60 rounded-lg transition-all"
                        >
                          <Edit2 size={16} />
                        </Link>
                        <button
                          onClick={() => handleDelete(event.id)}
                          title="Delete"
                          className="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800/60 rounded-lg transition-all"
                        >
                          <Trash2 size={16} />
                        </button>

                        <span className="w-px h-4 bg-slate-800 mx-1"></span>

                        {/* Status changers */}
                        {event.status !== 'published' && (
                          <button
                            onClick={() => handleStatusChange(event, 'published')}
                            title="Publish"
                            className="p-2 text-emerald-500/60 hover:text-emerald-400 hover:bg-slate-800/60 rounded-lg transition-all"
                          >
                            <Globe size={16} />
                          </button>
                        )}
                        {event.status !== 'draft' && (
                          <button
                            onClick={() => handleStatusChange(event, 'draft')}
                            title="Revert to Draft"
                            className="p-2 text-amber-500/60 hover:text-amber-400 hover:bg-slate-800/60 rounded-lg transition-all"
                          >
                            <FileText size={16} />
                          </button>
                        )}
                        {event.status !== 'archived' && (
                          <button
                            onClick={() => handleStatusChange(event, 'archived')}
                            title="Archive"
                            className="p-2 text-slate-400/60 hover:text-slate-300 hover:bg-slate-800/60 rounded-lg transition-all"
                          >
                            <Archive size={16} />
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
};

export default EventList;
