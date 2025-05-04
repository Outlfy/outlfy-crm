@if (isset($attribute))
    <v-url-component :attribute="{{ json_encode($attribute) }}" :validations="'{{ $validations }}'"
        :value="{{ json_encode(old($attribute->code) ?? $value) }}">
        <div class="mb-2 flex items-center">
            <input type="text"
                class="w-full rounded rounded-r-none border border-gray-200 px-2.5 py-2 text-sm font-normal text-gray-800 hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400">

            <div class="relative">
                <select
                    class="custom-select w-full rounded rounded-l-none border bg-white px-2.5 py-2 text-sm font-normal text-gray-800 hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:mr-6 ltr:pr-8 rtl:ml-6 rtl:pl-8">
                    <option value="linkedin" selected>@lang('admin::app.common.custom-attributes.linkedin')</option>
                    <option value="website">@lang('admin::app.common.custom-attributes.website')</option>
                    <option value="other">@lang('admin::app.common.custom-attributes.other')</option>
                </select>
            </div>
        </div>

        <span class="flex cursor-pointer items-center gap-2 text-brandColor">
            <i class="icon-add text-md !text-brandColor"></i>

            @lang('admin::app.common.custom-attributes.add-more')
        </span>
    </v-url-component>
@endif

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-url-component-template"
    >
        <template v-for="(url, index) in urls">
            <div class="mb-2 flex items-center">
                <x-admin::form.control-group.control
                    type="text"
                    ::id="attribute.code"
                    ::name="`${attribute['code']}[${index}][value]`"
                    class="rounded-r-none"
                    ::rules="getValidation"
                    ::label="attribute.name"
                    v-model="url['value']"
                    ::disabled="isDisabled"
                />

                <div class="relative">
                    <x-admin::form.control-group.control
                        type="select"
                        ::id="attribute.code"
                        ::name="`${attribute['code']}[${index}][label]`"
                        class="rounded-l-none ltr:mr-6 ltr:pr-8 rtl:ml-6 rtl:pl-8"
                        rules="required"
                        ::label="attribute.name"
                        v-model="url['label']"
                        ::disabled="isDisabled"
                    >
                        <option value="linkedin">@lang('admin::app.common.custom-attributes.linkedin')</option>
                        <option value="website">@lang('admin::app.common.custom-attributes.website')</option>
                        <option value="other">@lang('admin::app.common.custom-attributes.other')</option>
                    </x-admin::form.control-group.control>
                </div>

                <i
                    v-if="urls.length > 1"
                    class="icon-delete ml-1 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                    @click="remove(url)"
                ></i>
            </div>

            <x-admin::form.control-group.error ::name="`${attribute['code']}[${index}][value]`"/>
            <x-admin::form.control-group.error ::name="`${attribute['code']}[${index}].value`"/>
        </template>

        <span
            class="flex cursor-pointer items-center gap-2 text-brandColor"
            @click="add"
            v-if="! isDisabled"
        >
            <i class="icon-add text-md !text-brandColor"></i>

            @lang("admin::app.common.custom-attributes.add-more")
        </span>
    </script>

    <script type="module">
        app.component('v-url-component', {
            template: '#v-url-component-template',

            props: ['validations', 'isDisabled', 'attribute', 'value'],

            data() {
                return {
                    urls: this.value || [{
                        'value': '',
                        'label': 'linkedin'
                    }],
                };
            },

            watch: {
                value(newValue, oldValue) {
                    if (JSON.stringify(newValue) !== JSON.stringify(oldValue)) {
                        this.urls = newValue || [{
                            'value': '',
                            'label': 'linkedin'
                        }];
                    }
                },
            },

            computed: {
                getValidation() {
                    return {
                        url: true,
                        unique_url: this.urls ?? [],
                        ...(this.validations === 'required' ? {
                            required: true
                        } : {}),
                    };
                },
            },

            created() {
                this.extendValidations();
            },

            methods: {
                add() {
                    this.urls.push({
                        'value': '',
                        'label': 'linkedin'
                    });
                },

                remove(url) {
                    this.urls = this.urls.filter(item => item !== url);
                },

                extendValidations() {
                    defineRule('unique_url', (value, urls) => {
                        if (!value || !value.length) return true;

                        const count = urls.filter(url => url.value === value).length;

                        if (count > 1) {
                            return 'This URL is already in use.';
                        }

                        return true;
                    });
                },
            },
        });
    </script>
@endPushOnce
