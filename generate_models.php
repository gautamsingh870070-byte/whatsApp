<?php
$models = [
    'WhatsAppAccount' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass WhatsAppAccount extends Model\n{\n    protected \$guarded = [];\n\n    public function user() { return \$this->belongsTo(User::class); }\n    public function phoneNumbers() { return \$this->hasMany(WhatsAppPhoneNumber::class); }\n    public function templates() { return \$this->hasMany(MessageTemplate::class); }\n}\n",
    'WhatsAppPhoneNumber' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass WhatsAppPhoneNumber extends Model\n{\n    protected \$guarded = [];\n\n    public function account() { return \$this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id'); }\n}\n",
    'Contact' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Contact extends Model\n{\n    protected \$guarded = [];\n\n    public function user() { return \$this->belongsTo(User::class); }\n    public function tags() { return \$this->belongsToMany(Tag::class); }\n}\n",
    'Tag' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Tag extends Model\n{\n    protected \$guarded = [];\n\n    public function contacts() { return \$this->belongsToMany(Contact::class); }\n}\n",
    'MessageTemplate' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass MessageTemplate extends Model\n{\n    protected \$guarded = [];\n    protected \$casts = ['components' => 'array'];\n\n    public function account() { return \$this->belongsTo(WhatsAppAccount::class, 'whatsapp_account_id'); }\n}\n",
    'Campaign' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Campaign extends Model\n{\n    protected \$guarded = [];\n\n    public function template() { return \$this->belongsTo(MessageTemplate::class, 'message_template_id'); }\n    public function recipients() { return \$this->hasMany(CampaignRecipient::class); }\n}\n",
    'CampaignRecipient' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass CampaignRecipient extends Model\n{\n    protected \$guarded = [];\n    protected \$casts = ['variables' => 'array'];\n\n    public function campaign() { return \$this->belongsTo(Campaign::class); }\n    public function contact() { return \$this->belongsTo(Contact::class); }\n}\n",
    'Message' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass Message extends Model\n{\n    protected \$guarded = [];\n\n    public function logs() { return \$this->hasMany(MessageLog::class); }\n}\n",
    'MessageLog' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass MessageLog extends Model\n{\n    protected \$guarded = [];\n    protected \$casts = ['raw_webhook_payload' => 'array'];\n}\n",
    'WebhookEvent' => "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Model;\n\nclass WebhookEvent extends Model\n{\n    protected \$guarded = [];\n    protected \$casts = ['payload' => 'array'];\n}\n",
];

foreach ($models as $name => $content) {
    file_put_contents(__DIR__ . "/app/Models/{$name}.php", $content);
}
echo "Models generated.\n";
