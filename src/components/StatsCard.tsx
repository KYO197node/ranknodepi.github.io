import React from 'react';
import { DivideIcon as LucideIcon } from 'lucide-react';

interface StatsCardProps {
  icon: LucideIcon;
  value: string | number;
  label: string;
  gradient?: string;
  subtitle?: string;
}

export const StatsCard: React.FC<StatsCardProps> = ({ 
  icon: Icon, 
  value, 
  label, 
  gradient = 'from-purple-500 to-pink-500',
  subtitle
}) => {
  return (
    <div className="group p-6 rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
      <div className={`inline-flex items-center justify-center w-12 h-12 bg-gradient-to-r ${gradient} rounded-xl mb-4 group-hover:scale-110 transition-transform duration-300`}>
        <Icon className="w-6 h-6 text-white" />
      </div>
      <div className="text-3xl font-bold text-white mb-2">{value}</div>
      <div className="text-gray-400 text-sm">{label}</div>
      {subtitle && (
        <div className="text-gray-500 text-xs mt-1">{subtitle}</div>
      )}
    </div>
  );
};