import React, { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { eventService } from '../services/api.js';
import { speakers, sponsors, schedules, galleries, faqs } from '../config/dummy.js';
import { Calendar, MapPin, Building, ArrowLeft, Edit2, Globe, Archive, FileText, CheckCircle, AlertCircle, Users, Award, Clock, Image as ImageIcon, HelpCircle } from 'lucide-react';

const EventDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [event, setEvent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [successMsg, setSuccessMsg] = useState('');
  const [activeTab, setActiveTab] = useState('speakers'); // speakers, sponsors, schedules, galleries, faqs

  const fetchEventDetails = async () => {
    try {
      setLoading(true);
      setError('');
      const response = await eventService.getByIdOrSlug(id);
      if (response.status === 'success') {
        setEvent(response.data);
      } else {
        setError('Event not found.');
      }
    } catch (err) {
      console.error(err);
      setError('Failed to retrieve event details. Check VITE_API_URL or Python API.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEventDetails();
  }, [id]);

  const handleStatusChange = async (newStatus) => {
    try {
      setError('');
      setSuccessMsg('');

      if (newStatus === 'published') {
        if (!event.title || !event.category || !event.venue || !event.start_date || !event.thumbnail || !event.banner) {
          setError('Event must have all required fields before publishing.');
          return;
        }
      }

      const updatedData = {
        ...event,
        status: newStatus,
        start_date: event.start_date,
        end_date: event.end_date
      };

      const response = await eventService.update(event.id, updatedData);
      if (response.status === 'success') {
        setSuccessMsg(`Event status updated to ${newStatus} successfully.`);
        setEvent(response.data);
      } else {
        setError(response.message || 'Failed to update status.');
      }
    } catch (err) {
      console.error(err);
      setError(err.response?.data?.message || 'Error occurred while updating status.');
    }
  };

  if (loading) {
    return (
      <div className="space-y-6 animate-pulse">
        <div className="h-64 bg-slate-900 border border-slate-800 rounded-2xl"></div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="md:col-span-2 h-96 bg-slate-900 border border-slate-800 rounded-2xl"></div>
          <div className="h-96 bg-slate-900 border border-slate-800 rounded-2xl"></div>
        </div>
      </div>
    );
  }

  if (error || !event) {
    return (
      <div className="space-y-4">
        <Link to="/events" className="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors">
          <ArrowLeft size={16} /> Back to Event List
        </Link>
        <div className="p-6 bg-red-950/20 border border-red-900/40 text-red-400 rounded-2xl flex items-center gap-3">
          <AlertCircle size={24} />
          <div>
            <p className="font-bold">Error</p>
            <p className="text-sm">{error || 'Event not found.'}</p>
          </div>
        </div>
      </div>
    );
  }

  const tabItems = [
    { id: 'speakers', label: 'Speakers', icon: Users, count: speakers.length },
    { id: 'sponsors', label: 'Sponsors', icon: Award, count: sponsors.length },
    { id: 'schedules', label: 'Schedules', icon: Clock, count: schedules.length },
    { id: 'galleries', label: 'Galleries', icon: ImageIcon, count: galleries.length },
    { id: 'faqs', label: 'FAQs', icon: HelpCircle, count: faqs.length },
  ];

  return (
    <div className="space-y-8 max-w-6xl mx-auto">
      {/* Back navigation & Actions */}
      <div className="flex justify-between items-center">
        <Link to="/events" className="inline-flex items-center gap-2 text-slate-400 hover:text-white font-semibold transition-colors">
          <ArrowLeft size={16} /> Back to Events
        </Link>
        <div className="flex gap-3">
          <Link
            to={`/events/${event.id}/edit`}
            className="flex items-center gap-2 px-4 py-2 bg-slate-850 hover:bg-slate-800 border border-slate-700/80 text-slate-200 rounded-xl transition-all text-sm font-semibold"
          >
            <Edit2 size={16} /> Edit
          </Link>

          <span className="w-px bg-slate-800 mx-1"></span>

          {event.status !== 'published' && (
            <button
              onClick={() => handleStatusChange('published')}
              className="flex items-center gap-2 px-4 py-2 bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-600/20 rounded-xl transition-all text-sm font-semibold"
            >
              <Globe size={16} /> Publish
            </button>
          )}
          {event.status !== 'draft' && (
            <button
              onClick={() => handleStatusChange('draft')}
              className="flex items-center gap-2 px-4 py-2 bg-amber-600/10 text-amber-400 border border-amber-500/20 hover:bg-amber-600/20 rounded-xl transition-all text-sm font-semibold"
            >
              <FileText size={16} /> Revert to Draft
            </button>
          )}
          {event.status !== 'archived' && (
            <button
              onClick={() => handleStatusChange('archived')}
              className="flex items-center gap-2 px-4 py-2 bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 rounded-xl transition-all text-sm font-semibold"
            >
              <Archive size={16} /> Archive
            </button>
          )}
        </div>
      </div>

      {/* Notifications */}
      {successMsg && (
        <div className="p-4 bg-emerald-950/30 border border-emerald-900/30 text-emerald-400 rounded-xl flex items-center gap-3 text-sm">
          <CheckCircle size={18} />
          <span>{successMsg}</span>
        </div>
      )}

      {/* Hero Banner Header */}
      <div className="relative h-72 rounded-2xl overflow-hidden border border-slate-800 shadow-xl bg-slate-900">
        {event.banner ? (
          <img src={event.banner} alt={event.title} className="w-full h-full object-cover" />
        ) : (
          <div className="w-full h-full bg-slate-900 flex items-center justify-center text-slate-600">No Banner Image</div>
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
        <div className="absolute bottom-6 left-8 right-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
          <div>
            <span
              className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border mb-3 ${
                event.status === 'published'
                  ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'
                  : event.status === 'archived'
                  ? 'bg-slate-500/10 text-slate-400 border-slate-500/20'
                  : 'bg-amber-500/10 text-amber-400 border-amber-500/20'
              }`}
            >
              {event.status.toUpperCase()}
            </span>
            <h1 className="text-3xl font-extrabold text-white tracking-tight">{event.title}</h1>
            <p className="text-indigo-300 font-semibold mt-1">{event.category}</p>
          </div>
        </div>
      </div>

      {/* Detail contents */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {/* Left main: Details & Info */}
        <div className="md:col-span-2 space-y-6">
          <div className="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-xl">
            <h2 className="text-lg font-bold text-slate-200 mb-4">Event Description</h2>
            <p className="text-slate-450 leading-relaxed text-sm whitespace-pre-wrap">
              {event.description || 'No description provided for this event.'}
            </p>
          </div>

          {/* Dummy subitems tabs */}
          <div className="bg-slate-900 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl">
            {/* Tabs Header */}
            <div className="flex border-b border-slate-800 bg-slate-950/20 overflow-x-auto">
              {tabItems.map((tab) => {
                const Icon = tab.icon;
                return (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`flex items-center gap-2 px-6 py-4 font-bold text-xs uppercase tracking-wider transition-all border-b-2 whitespace-nowrap ${
                      activeTab === tab.id
                        ? 'border-indigo-500 text-indigo-400'
                        : 'border-transparent text-slate-400 hover:text-slate-200'
                    }`}
                  >
                    <Icon size={14} />
                    {tab.label}
                    <span className="ml-1 text-[10px] bg-slate-800 px-2 py-0.5 rounded-full text-slate-400">
                      {tab.count}
                    </span>
                  </button>
                );
              })}
            </div>

            {/* Tab content bodies */}
            <div className="p-6">
              {activeTab === 'speakers' && (
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  {speakers.map((speaker, idx) => (
                    <div key={idx} className="flex gap-4 p-4 bg-slate-950/40 border border-slate-800/60 rounded-xl">
                      <img src={speaker.photo} alt={speaker.name} className="w-12 h-12 rounded-full object-cover shrink-0 bg-slate-800" />
                      <div>
                        <h4 className="font-bold text-sm text-slate-200">{speaker.name}</h4>
                        <p className="text-xs text-indigo-400 font-semibold">{speaker.title}</p>
                        <p className="text-xs text-slate-500 mt-2 leading-relaxed">{speaker.bio}</p>
                      </div>
                    </div>
                  ))}
                </div>
              )}

              {activeTab === 'sponsors' && (
                <div className="space-y-6">
                  {['gold', 'silver', 'bronze', 'media_partner'].map((tier) => {
                    const filtered = sponsors.filter(s => s.tier === tier);
                    if (filtered.length === 0) return null;
                    return (
                      <div key={tier} className="space-y-3">
                        <h4 className="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{tier.replace('_', ' ')} Sponsor</h4>
                        <div className="flex flex-wrap gap-4">
                          {filtered.map((s, idx) => (
                            <a
                              href={s.website}
                              target="_blank"
                              rel="noopener noreferrer"
                              key={idx}
                              className="p-3 bg-slate-950/40 hover:bg-slate-950/80 border border-slate-800/60 hover:border-slate-700 rounded-xl transition-all flex items-center gap-3 shrink-0"
                            >
                              <img src={s.logo} alt={s.name} className="h-8 w-auto object-contain rounded-md" />
                              <span className="text-xs font-semibold text-slate-350">{s.name}</span>
                            </a>
                          ))}
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}

              {activeTab === 'schedules' && (
                <div className="space-y-4">
                  {schedules.map((schedule, idx) => (
                    <div key={idx} className="flex items-center gap-6 p-4 bg-slate-950/40 border border-slate-800/60 rounded-xl text-sm">
                      <div className="flex items-center gap-1.5 text-indigo-400 font-bold shrink-0">
                        <Clock size={14} />
                        <span>{schedule.start_time} - {schedule.end_time}</span>
                      </div>
                      <div className="w-px h-5 bg-slate-800 shrink-0"></div>
                      <p className="font-semibold text-slate-200">{schedule.title}</p>
                    </div>
                  ))}
                </div>
              )}

              {activeTab === 'galleries' && (
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                  {galleries.map((img, idx) => (
                    <div key={idx} className="aspect-video rounded-xl overflow-hidden border border-slate-800 shadow-md">
                      <img src={img} alt="Gallery item" className="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                    </div>
                  ))}
                </div>
              )}

              {activeTab === 'faqs' && (
                <div className="space-y-4">
                  {faqs.map((faq, idx) => (
                    <div key={idx} className="p-4 bg-slate-950/40 border border-slate-800/60 rounded-xl text-sm">
                      <p className="font-bold text-slate-200 flex gap-2">
                        <span className="text-indigo-400 font-extrabold">Q:</span>
                        {faq.question}
                      </p>
                      <p className="text-slate-450 mt-2 leading-relaxed pl-5 flex gap-2">
                        <span className="text-indigo-400/50 font-extrabold">A:</span>
                        {faq.answer}
                      </p>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Right sidebar: Event Metadata info card */}
        <div className="space-y-6">
          <div className="bg-slate-900 border border-slate-800/80 rounded-2xl p-6 shadow-xl space-y-6">
            <h3 className="text-base font-bold text-slate-200 border-b border-slate-800 pb-3">Event Information</h3>

            {/* Info rows */}
            <div className="space-y-4">
              <div className="flex items-start gap-3">
                <MapPin className="text-indigo-400 shrink-0 mt-0.5" size={18} />
                <div>
                  <p className="text-xs text-slate-500 font-semibold">Venue</p>
                  <p className="text-sm font-bold text-slate-200 mt-0.5">{event.venue}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <Building className="text-indigo-400 shrink-0 mt-0.5" size={18} />
                <div>
                  <p className="text-xs text-slate-500 font-semibold">Organizer</p>
                  <p className="text-sm font-bold text-slate-200 mt-0.5">{event.organizer}</p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <Calendar className="text-indigo-400 shrink-0 mt-0.5" size={18} />
                <div>
                  <p className="text-xs text-slate-500 font-semibold">Date & Time</p>
                  <p className="text-sm font-bold text-slate-200 mt-0.5">
                    {event.start_date ? new Date(event.start_date).toLocaleDateString('en-US', {
                      weekday: 'short',
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    }) : '-'}
                  </p>
                  {event.start_date && (
                    <p className="text-xs text-slate-400 mt-1">
                      Time: {new Date(event.start_date).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
                    </p>
                  )}
                </div>
              </div>
            </div>

            {/* Thumbnail preview */}
            <div className="border-t border-slate-800 pt-4 space-y-2">
              <p className="text-xs text-slate-500 font-semibold">Thumbnail Image</p>
              <div className="aspect-square rounded-xl overflow-hidden border border-slate-800 bg-slate-950">
                {event.thumbnail ? (
                  <img src={event.thumbnail} alt="Thumbnail preview" className="w-full h-full object-cover" />
                ) : (
                  <div className="w-full h-full bg-slate-950 flex items-center justify-center text-slate-600">No Image</div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EventDetail;
